<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use DB;
use App\Industry;

class IndustryController extends Controller
{
    private $core;
    public function __construct()
    {
        $this->core=app(\App\Http\Controllers\CoreController::class);
    }
    public function addIndustry(Request $request)
    {
         $newRec = Industry::updateOrCreate(['id'=>$request->id],['name'=>$request->name,'parent'=>$request->parent]);
        return response()->json($newRec);
    }
    public function addFamily(Request $request)
    {
         $newRec = Industry::updateOrCreate(['id'=>$request->id],['name'=>$request->name,'parent'=>$request->parent]);
        return response()->json($newRec);
    }
    public function getIndustry()
    {
        $data=Industry::all();
        return response()->json($data);
    }
    public function getFamily()
    {
        $data=Industry::where('parent',NULL)->orderBy('id','desc')->get();
        return response()->json($data);
    }
    public function DeleteIndustry($id)
    {
        $res=0;
        if(Industry::where('id',$id)->delete())
            $res = 1;
        return response()->json($res);
    }
}