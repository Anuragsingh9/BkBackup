<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Hash,Auth;
use DB;
use App\Task;
use App\Topic;
use App\TopicNote;
use App\TopicDocuments;
use App\Token;
use App\Meeting;
use App\WorkshopMeta;
use App\User;
use App\Workshop;
use App\Hostname as HostnameModel;
use Eloquent;
use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;
use Hyn\Tenancy\Environment;
use Illuminate\Contracts\Foundation\Application;

class RepdOfflineController extends Controller
{		
	private $tenancy;
	
    public function __construct()
    {
    	$this->tenancy=app(\Hyn\Tenancy\Environment::class);
    }

		public function login(Request $request){ 

			if(DB::connection('mysql')->table('hostnames')->where('fqdn','like','%'.$request->url.'%')->count()>0) {
				
				$host=Hostname::where('fqdn',$request->url)->first();
				$this->tenancy->hostname($host);
				if($request->email && $request->password)
		       {

		            if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
		            	if(Auth::user()->role=='M2'){
		            		$workshops=WorkshopMeta::where('user_id',Auth::user()->id)->where('role',1)->pluck('workshop_id');
		            		if(count($workshops)==0){
		            			return response()->json(['status'=>401,'api_token'=>'','msg'=>'Not allow']);
		            		}
		            	}

		            	$token_data=['api_token'=>generateRandomString(43),'remember_token'=>generateRandomString(64),'expired'=>date('Y-m-d H:i:s',strtotime('+7 hours'))];
		               	$res = Token::updateOrCreate(['user_id'=>Auth::user()->id],$token_data);
		               	return response()->json(['status'=>200,'api_token'=>$res->api_token,'user_data'=>Auth::user(),'msg'=>'Successfully loggedin !']);
		            }
		        }
		        return response()->json(['status'=>401,'api_token'=>'','msg'=>'Invalid Credentials !']);
		    } else {
		    	return response()->json(['status'=>401,'api_token'=>'','msg'=>'Host url not exist !']);
		    }
		 }
	   	 public function setOld(Hostname $hostname){
        		$this->old = $hostname;
				return $this;
    	}
	    public function updateMeetingData(Request $request){
	    	$post_data=[];
	    	if($request->col_offline==1){
	    		$post_data=['is_offline'=>$request->is_offline];
	    	} else if($request->col_download==1){
	    		$post_data=['is_downloaded'=>$request->is_downloaded];
	    	}
	    	if(!empty($post_data)){
		    	if(Meeting::where('id',$request->meeting_id)->update($post_data)){
		    		return response()->json(['status'=>200,'msg'=>'Record successfully updated !']);
		    	}
		    }
	    	return response()->json(['status'=>401,'msg'=>'Record updated failed!']);
	    }


		function getOfflineMeetings(Request $request){
				
			
				$data['meetings']=$this->getMeetingData($request->header('user-id'),$request->ids);
					// return response($this->getMeetingData($request->header('user-id'),$request->ids));
				return response()->json(['data'=>$data,'status'=>200]);
		}
		function getMeetingData($user_id,$request){
			$data=[];
			$records=[];
			$userDetail=User::find($user_id);
			if($userDetail->role=='M2'){
				 
					$workshops=WorkshopMeta::where('user_id',$user_id)->where('role',1)->pluck('workshop_id');

					$m_data =Meeting::with('workshop','topics')->select('id','name','description','place','date','start_time','end_time','workshop_id','redacteur','is_downloaded','is_offline')->where('is_offline','1')
						->whereIn('workshop_id',$workshops)
						->whereNotIn('id',json_decode($request))
						->get();
			}else{
					if($userDetail->role!='M3')
					$m_data =Meeting::with('workshop','topics')->select('id','name','description','place','date','start_time','end_time','workshop_id','redacteur','is_downloaded','is_offline')->where('is_offline','1')
						->whereNotIn('id',json_decode($request))
						->get();
					else{
						$m_data=[];
					}	
			}	
			if($m_data->count()>0){	
					foreach($m_data as $val){
					$meeting_id[]=$val->id;
					$records[]=['id'=>$val->id,
							'name'=>$val->name,
							'description'=>$val->description,
							'place'=>$val->place,
							'date'=>$val->date,
							'start_time'=>$val->start_time,
							'end_time'=>$val->end_time,
							'redacteur'=>$val->redacteur,
							'is_downloaded'=>$val->is_downloaded,
							'workshop_name'=>$val->workshop->workshop_name,
							'workshop_id'=>$val->workshop->id,
							'is_offline'=>$val->is_offline,
							'validated_prepd'=>$val->validated_prepd,
							'validated_repd'=>$val->validated_repd,
							'topics'=>$val->topics
						];

					}
						$data['meeting']=$records;
						$data['notes']=TopicNote::where('user_id', $user_id)->whereIn('meeting_id', $meeting_id)->get();
				}else{
						$data['meeting']=[];
						$data['notes']=[];
				}	
				return $data;
		}
		function repdDownloadStatus(Request $request){
			
			//Meeting::select('id','name','description','place','date','start_time','end_time','workshop_id','redacteur','is_downloaded')->where('is_offline','1')->get()->toArray();
			//return response()->json(['data'=>$data,'status'=>200]);
		}	
		function getrepdData(Request $request){
				
				$data['topics']=Topic::where('meeting_id',$request->meeting_id)->get()->toArray();
				//$data['meeting']=Meeting::where('')->find($request->meeting_id);
				$workshop_id=Meeting::where('id',$request->meeting_id)->pluck('workshop_id');
				$data['workshop_meta']=WorkshopMeta::select('id','workshop_id','user_id','role')->whereIn('id',$workshop_id)->get()->toArray();
				foreach ($data['workshop_meta'] as $key => $value) {
					$user_id[]=$value['user_id'];
				}
				$data['user_info']=User::select('id','fname','lname','email')->whereIn('id',$user_id)->get()->toArray();
				return response()->json(['data'=>$data,'status'=>200]);
		}
		function saveTask(Request $request){
			$data=json_decode($request->data);
			$id=$request->meeting_id;
			Topic::update(['discussion'=>$request->discussion,'decision'=>$request->decision,'reuse'=>$request->resue])->where('meeting_id',$request->meeting_id);
				foreach($data['task'] as $row){
				$lastId=Task::insert(['task_created_by_id'=>$row->task_created_by_id,'task_text'=>$row->task_next,'start_date'=>$row->start_date,'end_date'=>$row->end_date,'status'=>$row->status])->lastInsertId();
				}
				
		}
				function uploadPrepdData(Request $request){
				$decodeData=json_decode($request->data);
				$meeting_data=$decodeData->meeting_data[0];
				$topic_data=$decodeData->topic_data;
				$topic_notes=$decodeData->topic_notes;
				$topicInsert=[];
				$topicNotesInsert=[];
				$newTopicIds=[];
				$unDeleteTopicId=[];
				$topicDocArray=[];
			$getMeetingTopicTopic=Topic::where('meeting_id',$meeting_data->id)->pluck('id');
				$topicDoc=TopicDocuments::whereIn('topic_id',$getMeetingTopicTopic)->get();
				$data=Meeting::where('id',$meeting_data->id)->update(['redacteur'=>$meeting_data->redacteur,'is_offline'=>0]);
				$delete=Topic::where('meeting_id',$meeting_data->id)->delete();
				foreach ($topic_data as $key => $value) {
						$unDeleteTopicId[]=$value->old_topic_id;
						$topicInsert=['grand_parent_id'=>($value->grand_parent_id=='' || $value->grand_parent_id==null)?null:$value->grand_parent_id,'parent_id'=>($value->parent_id=='' || $value->parent_id==null)?null:$value->parent_id,'level'=>$value->level,'topic_title'=>$value->topic_title,'meeting_id'=>$value->meeting_id,'workshop_id'=>$value->workshop_id,'discussion'=>$value->discussion,'decision'=>$value->decision,'list_order'=>$value->list_order,'reuse'=>$value->reuse];
							if($value->parent_id!=null && $value->parent_id!=''){
								$topicInsert['parent_id']=(isset($newTopicIds[$value->parent_id]))?$newTopicIds[$value->parent_id]:null;
							}
							if($value->grand_parent_id!=null && $value->grand_parent_id!=''){
								$topicInsert['grand_parent_id']=(isset($newTopicIds[$value->grand_parent_id]))?$newTopicIds[$value->grand_parent_id]:null;
							}

						$last_id=Topic::insertGetId($topicInsert);
							foreach ($topic_notes as $key => $topicNote) {
								if($topicNote->topic_id==$value->id){
									$topicNotesInsert[]=['meeting_id'=>$topicNote->meeting_id,'topic_id'=>$last_id,'user_id'=>$topicNote->user_id,'topic_note'=>$topicNote->topic_note];
								}
								
							}
						$newTopicIds[$value->id]=$last_id;
						if(count($topicDocArray)>0 && isset($topicDocArray[$value->old_topic_id])){
							$topicDocArray[$value->old_topic_id]['topic_id']=$last_id;
						}
						if(count($topicDoc)>0){
							foreach ($topicDoc as $k => $topicDocData) {
								if($topicDocData->topic_id==$value->old_topic_id)
								$topicDocArray[]=['topic_id'=>$last_id,'document_id'=>$topicDocData->document_id,'created_by_user_id'=>$topicDocData->created_by_user_id];
							}
						}
				}
				if(count($topicDocArray)>0){
						$data=TopicDocuments::insert($topicDocArray); 
				}
				if(count($topicNotesInsert)>0){
						$data=TopicNote::insert($topicNotesInsert); 
				}
				return response()->json($data);	
				
		}
		
		// function uploadRepdData(Request $request){
		// 	$decodeData=json_decode($request->data);
		// 	$topic_data=$decodeData->topic_data;
		// 	$topic_notes=$decodeData->topic_notes;
		// 	$decodeData=json_decode($request->data);
		// 		$meeting_data=$decodeData->meeting_data[0];
		// 		$topic_data=$decodeData->topic_data;
		// 		$topic_notes=$decodeData->topic_notes;
		// 		$topicInsert=[];
		// 		$topicNotesInsert=[];
		// 		$newTopicIds=[];
		// 		$data=Meeting::where('id',$meeting_data->id)->update(['redacteur'=>$meeting_data->redacteur,'is_offline'=>0]);
		// 		foreach ($topic_data as $key => $value) {
		// 				$data=Topic::where('id',$value->id)->update(['discussion'=>$value->discussion,'decision'=>$value->decision,'reuse'=>$value->reuse]);
		// 		}
		// 		foreach ($topic_notes as $key => $value) {
		// 				$data=TopicNote::where('id',$value->id)->update(['topic_note'=>$value->topic_note]);
		// 		}
		// 		return response()->json($data);	
		// }


}