<?php

namespace App\Http\Controllers\Project;

use App\Model\TaskDependency;
use App\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Auth;

class TaskDependencyController extends Controller
{
    /*
     * get all parent task of given task
     * getting task dependency
     * */
    public function getParentTask(Task $task)
    {
        $dependency = TaskDependency::where('child_id', $task->id)->with('dependent.task_user_info','dependent.milestone:id,project_id','dependent.workshopRelate')->orderBy('id','desc')->paginate(10,['id', 'parent_id']);
        return response()->json([
            'status' => TRUE,
            'data' => $dependency
        ],200);
    }

    /*
     * get all child task of given task
     * getting task dependent
     * */
    public function getChildTask(Task $task)
    {

        $dependency = TaskDependency::with('dependency.task_user_info','dependency.milestone:id,project_id','dependency.workshopRelate')->where('parent_id', $task->id)->orderBy('id','desc')->paginate(10,['id', 'child_id']);

        return response()->json([
            'status' => TRUE,
            'data' => $dependency
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function addDependent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_id' => 'required',
            'child' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
        }

        //checking same entry exist or not

        $check=TaskDependency::where(['parent_id'=>$request->task_id,'child_id'=>$request->child,'created_by_id'=>Auth::user()->id])->count();
        if($check==0){
            $taskDependent=TaskDependency::create([
                'parent_id'=>$request->task_id,
                'child_id'=>$request->child,
                'created_by_id'=>Auth::user()->id,
            ]);
            $dependent = TaskDependency::where('id', $taskDependent->id)->with('dependency.task_user_info','dependency.milestone:id,project_id','dependency.workshopRelate')->first();
            return response()->json([
                'status' => TRUE,
                'data' => $dependent
            ],200);
        }else{

            return response()->json(['status' => FALSE, 'msg' => 'Already Exists'], 201);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function addDependency(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_id' => 'required',
            'parent' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 201);
        }
        $check=TaskDependency::where(['parent_id'=>$request->parent,'child_id'=>$request->task_id,'created_by_id'=>Auth::user()->id])->count();
        if($check==0) {
            $taskDependency = TaskDependency::create([
                'parent_id' => $request->parent,
                'child_id' => $request->task_id,
                'created_by_id' => Auth::user()->id,
            ]);
            $dependency = TaskDependency::where('id', $taskDependency->id)->with('dependent.task_user_info', 'dependent.milestone:id,project_id', 'dependent.workshopRelate')->first();

            return response()->json([
                'status' => TRUE,
                'data' => $dependency
            ], 200);

        }else{

            return response()->json(['status' => FALSE, 'msg' => 'Already Exists'], 201);
        }
    }
    public function deleteDependancy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
        }
        $data=TaskDependency::where('id',$request->id)->delete();
        if($data){
             return response()->json($data,200);
        }else{
             return response()->json($data,200);
        }

    }

}
