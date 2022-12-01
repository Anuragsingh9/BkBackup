<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use DB;
use App\Issuer;

class IssuerController extends Controller
{
    private $core;
    public function __construct()
    {
        $this->core=app(\App\Http\Controllers\CoreController::class);
    }
    public function addIssuer(Request $request)
    {
        $newRec = Issuer::updateOrCreate(['id'=>$request->id],['issuer_name'=>$request->issuer_name,'issuer_code'=>$request->issuer_code]);
        return response()->json($newRec);
    }
    public function getIssuers()
    {
        $data=Issuer::all();
        return response()->json($data);
    }
    public function deleteIssuers($id)
    {
        $res=0;
        if(Issuer::where('id',$id)->delete())
            $res = 1;
        return response()->json($res);
    }
}
