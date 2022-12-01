<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Hash;
use DB;
use App\ResourcesCategory;
use App\Resources;
use AWS;
use App\Group;

class ResourcesController extends Controller
{
    private $core;
    public function __construct()
    {
        $this->core=app(\App\Http\Controllers\CoreController::class);
    }
    public function getAllGroup(Request $request)
    {
        if($request->id>0){
            $data= DB::connection('tenant')->select('SELECT g.* ,(SELECT count(*) FROM resources_categories WHERE find_in_set(g.id,group_id) AND id='.$request->id.') as is_checked FROM groups as g');
        }
        else {
            $data=Group::get();
        }
        return response()->json($data);
    }

    public function addResourcesCategory(Request $request)
    { 

        $postData = ['category_name'=>$request->category_name,'category_desc'=>$request->category_desc,'resources_type'=>$request->resources_type,'group_id'=>$request->group_id];
        if($request->parent!=null)
            $postData['parent']=$request->parent;
        if($request->is_public!=null)
            $postData['is_public']=$request->is_public;
        if($request->is_private!=null)
            $postData['is_private']=$request->is_private;
        
        $newRec = ResourcesCategory::updateOrCreate(['id'=>$request->resources_id],$postData);
        return response()->json($newRec);
    }
    public function getResourcesCategory(Request $request)
    {
        if($request->id>0){
            $data= DB::connection('tenant')->select('SELECT r.* ,(SELECT count(*) FROM resources WHERE find_in_set(r.id,resources_category_id) AND id='.$request->id.') as is_checked FROM resources_categories as r');
        }
        else {
           $data= DB::connection('tenant')->select('SELECT r.* ,(SELECT count(*) FROM resources WHERE find_in_set(r.id,resources_category_id)) as count FROM resources_categories as r');
        }
        return response()->json($data);
    }
    public function addResources(Request $request)
    {

       if($request->res_file!=''){
            $folder = 'uploads/resources';
            $filename=$this->core->fileUploadByS3($request->file('res_file'),$folder,'public');
            $request->merge(['resources_file'=>$filename]);
        } 
        $lastRec = Resources::updateOrCreate(['id'=>$request->id],$request->except(['res_file']));
        return response()->json($lastRec);     
    }  
    public function DeleteResources($id){
        $data = Resources::where('id',$id)->delete();
        return response()->json($data);
    }
    public function getResources($cat_id=null){
        if($cat_id!=null){
            $data = Resources::with('cate')->where('resources_category_id',$cat_id)->get();
        } else {
            $data['resources'] = Resources::with('cate')->get();
            $data['resources_category'] = ResourcesCategory::get();

        }
        return response()->json($data); 
    }
    public function DeleteResourcesCategory($id)
    {
        $data = ResourcesCategory::where('id',$id)->delete();
        return response()->json($data);
    }
    public function getResourcesById($id){
        $data  = Resources::where('id',$id)->first();
        if($data->resources_file!='')
            $data->resources_file=$this->core->getS3Parameter($data->resources_file,2);
        return response()->json($data);
    } 


    public function editResources(Request $request){
        if($request->hasFile('res_file')){
            $folder = 'uploads/resources';            
            $filename=$this->core->fileUploadByS3($request->file('res_file'),$folder,'public');
            $request->merge(['resources_file'=>$filename]);
        }
        return response()->json(Resources::where('id',$request->id)->update($request->except(['res_file'])));
    }
}
