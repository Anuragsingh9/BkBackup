<?php

namespace App\Http\Controllers\ImproveMentFour;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\User;
use DB;
class NewMemberAlert extends Controller
{
    public function getOrgAdminUser($val){
        try{
            $data= User::where('role','M1')->where('alert_new_member',false)->where(DB::raw('CONCAT(email," ", lname," ",fname)'), 'like', '%' . $val . '%')->groupBy('email')->get(['id','email','role','fname','lname']);
            return response()->json(['status'=>true,'data'=>$data]);
        }catch(\Exception $e){
            return response()->json(['status'=>false,'data'=>$e->getMessage()]);
        }  
}
public function getOrgAdminAlertUser(){
        try{
            $data= User::where('role','M1')->where('alert_new_member',true)->get(['id','email','role','fname','lname','alert_new_member']);
            return response()->json(['status'=>true,'data'=>$data]);
        }catch(\Exception $e){
            return response()->json(['status'=>false,'data'=>$e->getMessage()]);
        }  
}
//update Email add and delete for new member alert 
public function updateAdminAlert(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'data' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 422); //validation false return errors
            }
            $res=0;
            $data=json_decode($request->data);
            $res= User::where('role','M1')->update(['alert_new_member'=>false]);
            if(count($data)>0){
                foreach($data as $row){
                    $res= User::where('id',$row->id)->update(['alert_new_member'=>$row->alert_new_member]);
                }
            }
            if($res){
                return response()->json(['status'=>true,'data'=>$data]);
            }
            return response()->json(['status'=>false,'data'=>$data]);
        }catch(\Exception $e){
            return response()->json(['status'=>false,'data'=>$e->getMessage()]);
        }  
}
}
