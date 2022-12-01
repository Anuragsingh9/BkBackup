<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use DB;
use App\Group;

class GroupController extends Controller
{
    private $core;
    public function __construct()
    {
        $this->core=app(\App\Http\Controllers\CoreController::class);
    }
    public function addGroup(Request $request)
    {
        $newRec = Group::updateOrCreate(['id'=>$request->id],['group_name'=>$request->group_name]);
        return response()->json($newRec);
    }
    public function getGroup()
    {
        $data=Group::all();
        return response()->json($data);
    }
    public function DeleteGroup($id)
    {
        $res=0;
        if(Group::where('id',$id)->delete())
            $res = 1;
        return response()->json($res);
    }
 
}
