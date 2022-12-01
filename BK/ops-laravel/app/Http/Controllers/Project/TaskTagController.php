<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Model\Tags;
use App\Model\TaskTag;
use Illuminate\Http\Request;
use Validator;
class TaskTagController extends Controller
{
    public function getTags()
    {
        $data = Tags::with('color')->get();
        return response()->json(['status' => true, 'data'=>$data],200);
    }
    public function addTaskTags(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'task_id' => 'required:unique:task_tags',
            'tag_id' => 'required:unique:task_tags',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
        }   
        $data = TaskTag::create(['task_id'=>$request->task_id,'tag_id'=>$request->tag_id]);
        if($data){
            $data=TaskTag::with('tag')->where('task_id',$request->task_id)->get();
            return response()->json(['status' => true, 'data'=>$data],200);
        }
        return response()->json(['status' => false, 'msg'=>'Error'],200);
    }
    public function getTagsByFilter(Request $request){
        $tag_id=TaskTag::where('task_id', $request->task_id)->get(['tag_id'])->pluck('tag_id');
        if($request->lang=='EN'){
            $data = Tags::with('color')->Where('en_name', 'like', '%' . $request->value . '%')->whereNotIn('id',$tag_id)->get();
        }else{
            $data = Tags::with('color')->Where('fr_name', 'like', '%' . $request->value . '%')->whereNotIn('id',$tag_id)->get();
        }
        return response()->json(['status' => true, 'data'=>$data],200);
    }
    public function taskTagDelete(Request $request){
        $result=TaskTag::where('id', $request->id)->delete();
        if($result){
             return response()->json(['status' => true, 'data'=>$result],200);
        }else{
            return response()->json(['status' => false, 'msg'=>'Error'],200);
        }
    }
    public function addTags(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'id'=>'required',
            'fr_name' => 'required',
            'en_name' => 'required',
            'color_id' => 'required',
            'description' => 'required',
            'type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
        }
        $data = Tags::updateOrCreate(['id'=>$request->id],['fr_name' => $request->fr_name,
            'en_name' => $request->en_name,
            'color_id' => $request->color_id,
            'type' => $request->type,
            'description' => $request->description,
        ]);
        if ($data) {
                  return response()->json(['status' => true, 'data' => $data], 200);
            }
        return response()->json(['status' => false, 'msg' => 'Error'], 200);
    }
    public function deleteTag($id){
        
        $data = Tags::where('id',$id)->delete();
        return response()->json(['status' => ($data)?true:false, 'msg'=>($data)?$data:'Error'],200); 
    }
}
