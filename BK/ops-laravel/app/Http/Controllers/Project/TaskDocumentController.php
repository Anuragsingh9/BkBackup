<?php

namespace App\Http\Controllers\Project;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\TaskDocument;
use App\RegularDocument;
use Auth;
class TaskDocumentController extends Controller{
    
      private $core;

    public function __construct(){
        $this->core = app(\App\Http\Controllers\CoreController::class);
    }
    public function index($task_id){
    	$data=TaskDocument::with('document')->where('task_id',$task_id)->orderBy('created_at','desc')->get(['id','document_id','task_id']);
    	return response()->json(['status'=>true,'data'=>$data]);
    }
     public function store(Request $request){
     			$task_id=$request->task_id;
     			unset($request['task_id']);
     			$doc=$this->addFiles($request);
				$data=TaskDocument::create(['task_id'=>$task_id,'document_id'=>$doc->id]);
     			return response()->json(['status'=>true,'data'=>$data]);
     }
     public function addFiles(Request $request){

        if ($request->hasFile('doc_file')) {
            $folder = 'uploads' . getDocType($request->document_type_id);
            $filename = $this->core->fileUploadByS3($request->file('doc_file'), $folder, 'public');
               if(empty(pathinfo($filename,PATHINFO_EXTENSION))){
                   $filename=$filename.$request->file('doc_file')->getClientOriginalExtension();
               }
            $request->merge(['document_file' => $filename]);
        }
        $request->merge(['increment_number' => 0, 'user_id' => Auth::user()->id, 'created_by_user_id' => Auth::user()->id]);
       return RegularDocument::create($request->except(['doc_file']));
    }
}
