<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;
use App\ActionLog;

class ActionLogController extends Controller
{
    public function __construct()
    {
        $this->unReadMsg=app(\App\Http\Controllers\MessageController::class); 
    }

    function logging(Request $request){
        $dataArray['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $dataArray['action'] = $request->path;
        $dataArray['user_id'] = $request->user_id;

        $exp = explode('/', $request->path);
        if(count($exp)>0){
            $dataArray['menu'] = $exp[1];
            unset($exp[1]);
            $chunks = array_filter($exp);
            $sub = implode('/', $chunks);
            $dataArray['sub_menu'] = $sub;
        }
        ActionLog::create($dataArray);
        return response()->json($this->unReadMsg->getUnreadMessageCount($request->user_id));
    }

    public function searchActionLog(Request $request){
        $query = ActionLog::query();
        if($request->user_id!=''){
            $query->where('user_id',$request->user_id);
        }
        if($request->start_date!='' && $request->end_date!=''){
            $query->whereBetween(DB::raw('date(created_at)'),array(date('Y-m-d',strtotime($request->start_date)),date('Y-m-d',strtotime($request->end_date))));
        }else {
            if($request->start_date!=''){
                $query->where(DB::raw('date(created_at)'),'>=',date('Y-m-d',strtotime($request->start_date)));
            }
            if($request->end_date!=''){
                $query->where(DB::raw('date(created_at)'),'<=',date('Y-m-d',strtotime($request->end_date)));
            }
        }
        $serachRes = $query->with('user')->paginate(25);
        return  response()->json($serachRes);
    }
}
