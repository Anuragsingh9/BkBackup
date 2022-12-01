<?php

namespace App\Http\Controllers\Project;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\TaskComment;
use Auth;
use App\Task;
use Validator;
class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($task_id)
    {
        $comment=TaskComment::with('user')->where('task_id',$task_id)->latest()->paginate(5);
       	
      		return response()->json([
	            'status' => TRUE,
	            'data' => $comment
	        ],200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(request $request)
    {
		$request->validate([
    	'task_id' => 'required',
    	'comment' => 'required',
		]);
		// preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $request->comment, $match);
		// foreach($match[0] as $k=>$item){
		// 	$newurl='<a class="data-link-open" href="'.$item.'" target="_blank">'.$item.'</a>';
		// 	$request->comment=str_replace($item,$newurl, $request->comment);
		// }
       $comment=TaskComment::create(['task_id'=>$request->task_id,'user_id'=>Auth::user()->id,'comment'=>$request->comment,'workshop_id'=>$request->wid]);
       if($comment){
	       return response()->json([
	            'status' => TRUE,
	            'data' => $comment
	        ],200);
   		}
   		else{
   			return response()->json([
	            'status' => false,
	            'data' => $comment
	        ],200);	
   		}
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
     	 $comment=TaskComment::where('id',$request->id)->delete();
     	 if($comment){
       		return response()->json([
	            'status' => TRUE,
	            'data' => $comment
	        ],200);
       	}
       	else{
       		return response()->json([
	            'status' => false,
	            'data' => $comment
	        ],200);
       	}
    }

    public function UpdateDescription(Request $request){
    	$task=Task::where('id',$request->id)->update(['description'=>$request->description]);
    	if($task){
    		return response()->json([
	            'status' => true,
	            'data' => $task
	        ],200);
    	}
    	else{
    		return response()->json([
	            'status' => false,
	            'data' => $task
	        ],200);
    	}
    }
    //Add Link in Task
     public function addTaskLink(Request $request)
    {
        if($request->type=='delete'){
                 $validator = Validator::make($request->all(), [
                 'task_id' => 'required',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 200);
                }
                $data = Task::where('id', $request->task_id)->update(['task_link' => $request->task_link]);
        }else{
                $validator = Validator::make($request->all(), [
                    'task_link' => 'required|url',
                    'task_id' => 'required',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 200);
                }
                $data = Task::where('id', $request->task_id)->update(['task_link' => $request->task_link]);

        }
            if ($data) {
                return response()->json([
                    'status' => true,
                    'data' => $data,
                ], 200);
            }
            return response()->json([
                'status' => false,
                'msg' => 'Error',
            ], 200);   
        
    }
}
