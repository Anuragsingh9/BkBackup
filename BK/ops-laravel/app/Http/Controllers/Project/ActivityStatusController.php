<?php

namespace App\Http\Controllers\Project;

use App\Model\ActivityStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Task;
class ActivityStatusController extends Controller
{
    private $task;
    public function __construct()
    {
        $this->task = app(\App\Http\Controllers\TaskController::class);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $status=ActivityStatus::orderBy('list_order','asc')->get(['id','fr_label','en_label']);
        return response()->json([
            'status' => true,
            'data' => $status
        ],200);
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
        /*|unique:activity_statuses.fr_label*//*|unique:activity_statuses.en_label*/
        $validator = Validator::make($request->all(), [
            'en_label' => 'required',
            'fr_label' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
        }

        if (isset($request->id) && !empty($request->id)) {
            $activityStatus = ActivityStatus::where('id', $request->id)->update(
                [
                    'en_label' => $request->en_label,
                    'fr_label' => $request->fr_label,
                ]);
            $activityStatus = ActivityStatus::find($request->id);

        } else {
            $activityStatus = ActivityStatus::create(
                [
                    'en_label' => $request->en_label,
                    'fr_label' => $request->fr_label,
                ]);

        }

        return response()->json([
            'status' => true,
            'data' => $activityStatus
        ], 200);

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
     * Update Activity status order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateActivtyStatusOrder(Request $request){
        $actvityIds = json_decode($request->ids);
        $update=0;
        if (count($actvityIds) > 0) {
            foreach ($actvityIds as $k => $val) {
               $update= ActivityStatus::where('id', $val)->update(['list_order' => $k]);
            }
        }
        if($update){
            return response()->json(['status'=>true,'data'=>ActivityStatus::orderBy('list_order','asc')->get()]);
        }
        return response()->json(['status'=>false,'msg'=>'Update Error']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if($request->id>3){
            $taskids=Task::where('status',$request->id)->get(['id'])->pluck('id');
            foreach($taskids as $data){
               $this->task->deleteTask(Task::find($data));
            }
            $res=ActivityStatus::destroy($request->id);
            if($res){
                return response()->json(['status'=>true,'data'=>$res]);
            }
        }
        return response()->json(['status'=>false,'data'=>$res]);
    }
}
