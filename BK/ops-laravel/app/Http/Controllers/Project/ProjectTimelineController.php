<?php

namespace App\Http\Controllers\Project;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\WorkshopMeta;
use App\Project;
use App\Model\ProjectTimelineOrder;
use Auth;
class ProjectTimelineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($workshop_id)
    {

        // $workshop_id = WorkshopMeta::where('user_id', Auth::user()->id)->get(['workshop_id'])->pluck('workshop_id')->toArray();
       // $workshop_id = array_unique($workshop_id); 
       // dd($workshop_id);
       $sortedProjectList=[]; 
        $project=Project::where('wid',$workshop_id)->with('milestone.tasks','project_timeline_order')->orderBy('project_label','ASC')->get(['id','project_label']);
        foreach($project as $key=>$value){
            if($value->project_timeline_order!=null){
               $sortedProjectList[$value->project_timeline_order->order-1]=$value ;
            }
            else{
             $sortedProjectList[]=$value ;
            }
        }
         return response()->json([
            'status' => TRUE,
            'data' => $sortedProjectList
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
       
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $projectIdArray=explode(',',$request->id);
       
        foreach($projectIdArray as $k=>$value){
            $status=ProjectTimelineOrder::updateOrCreate(
                 ['user_id' => Auth::user()->id,'wid' => $request->workshop_id,'project_id' => $value],
                ['wid' => $request->workshop_id, 'user_id' => Auth::user()->id, 'project_id' => $value, 'order' => ($k+1)]
            );
        }

       $sortedProjectList=[]; 
       $workshop_id=$request->workshop_id;
        $project=Project::where('wid',$workshop_id)->with('milestone.tasks','project_timeline_order')->orderBy('project_label','ASC')->get(['id','project_label']);
        foreach($project as $key=>$value){
            if($value->project_timeline_order!=null){
               $sortedProjectList[$value->project_timeline_order->order-1]=$value ;
            }
            else{
             $sortedProjectList[]=$value ;
            }
        }
        if($status){
            return response()->json([
                'status' => TRUE,
                'data' => $sortedProjectList
            ], 200);
        }
        else{
         return response()->json([
                'status' => false,
                'data' => $sortedProjectList
            ], 200);   
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function updateProjectLabel(Request $request){
        $project=Project::where('id',$request->id)->update(['project_label'=>$request->project_name]);

        if($project){
             return response()->json([
                'status' => TRUE,
                'data' => $project
            ], 200);
        }
        else{
            return response()->json([
                'status' => false,
                'data' => $project
            ], 200);
        }
    }
}
