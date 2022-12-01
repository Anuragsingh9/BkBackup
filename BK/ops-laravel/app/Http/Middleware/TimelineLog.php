<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Auth;
use App\Timeline;
class TimelineLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
	
	   // if (!$request->secure() && env('APP_ENV') === 'local') {
		  //       //URL::forceSchema('https');
    //             return redirect()->secure($request->getRequestUri());
    //     }
        $action_type = $request->route()->methods[0];
        $curr_actions=[
            'method'=>$action_type,
            'function'=>explode('@',$request->route()->action['uses'])[1],
            'params'=>($action_type=='POST') ? $request->all() : $request->route()->parameters, 
            'log_text'=>''
        ];
        
       $this->saveTimeline($curr_actions);
        return $next($request);
    }

    public function saveTimeline($curr_actions){
            
            $flag=false;
            $dataArray=[];
            $params = $curr_actions['params'];

            switch($curr_actions['function']){
                // case 'getWorkshopMembers':
                    
                case 'addMeeting':
                    $flag=true;
                    $dataArray = ['workshop_id'=>$params['workshop_id'],'type'=>'meeting','description'=>$this->stringLength($params['description']),'action'=>'lang_en_add_new_metting'];
                    break;
                // case 'editMeeting':
                //         $flag=true;
                //         $dataArray = ['workshop_id'=>$params['workshop_id'],'type'=>'meeting','description'=>'a modifié une réunion '.$params['name'],'action'=>'lang_en_modified_a_meeting'];
                //         break;
                case 'addTopic':
                        $flag=true;
                        $dataArray = ['workshop_id'=>$params['workshop_id'],'type'=>'meeting','description'=>'a créé un ordre du jour ','action'=>'lang_en_create_a_agenda'];
                        break;
                // case 'saveTopicText':
                //         $dataArray = ['workshop_id'=>$params['workshop_id'],'type'=>'meeting','description'=>'a créé un ordre du jour ','action'=>'lang_en_create_a_agenda'];
                //         break;
                case 'addMessage':
                    $flag=true;
                    $dataArray = ['workshop_id'=>$params['workshop_id'],'type'=>'message','description'=>$this->stringLength($params['messages_text']),'action'=>'lang_en_add_new_message'];
                    break;
                case 'saveTask':
                    $flag=true;
                    $dataArray = ['workshop_id'=>isset($params['workshop_id'])?$params['workshop_id']:0,'type'=>'task','description'=>$this->stringLength($params['task_text']),'action'=>'lang_en_add_new_task'];
                    break;
                case 'addFiles':
                    $flag=true;
                    $dataArray = ['workshop_id'=>$params['workshop_id'],'type'=>'file','description'=>$this->stringLength($params['document_title']),'action'=>'lang_en_add_new_file'];
                    break;
                case 'addMember':
                        $flag=true;
                        $dataArray = ['workshop_id'=>$params['workshop_id'],'type'=>'file','description'=>'a ajouté un membre','action'=>'lang_en_add_new_member'];
                        break;
                case 'updateMemberStatus':
                        $flag=true;
                        if($params['status']==1){
                           $dataArray = ['workshop_id'=>$params['wid'],'type'=>'file','description'=>'a désigné un nouveau Secrétaire','action'=>'lang_en_assign_new_secretary']; 
                        }
                        if($params['status']==2){
                            $dataArray = ['workshop_id'=>$params['wid'],'type'=>'file','description'=>'a désigné un nouveau Validateur','action'=>'lang_en_assign_new_validater']; 
                        }
                        break;

                 // case 'deleteMember':

                 //        $dataArray = ['workshop_id'=>session()->get('wid'),'type'=>'file','description'=>'a retiré un membre','action'=>'lang_en_remove_a_member'];
                 //        break;
                        

            }

            if($flag){
                $dataArray['user_id'] =Auth::user()->id;
                Timeline::create($dataArray);
            }
    }
    function stringLength($string,$length=50){
        return $string;
    }
}
