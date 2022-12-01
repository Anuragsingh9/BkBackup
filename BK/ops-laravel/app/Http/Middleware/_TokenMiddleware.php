<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
use DB;
class _TokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null){
        if($request->header('Api-Token') && ($request->header('userId') && $request->header('Api-Token')!='')){
            $rs = DB::table('tokens')->where('remember_token',$request->header('API-Token'))->where('user_id',$request->header('userId'))->get();
            if(count($rs)>0){
                $token['api_token'] = generateRandomString(43);
                $token['expired'] = date('Y-m-d H:i:s',strtotime('+3 hours'));
                DB::table('tokens')->update($token);
                $token['status'] = 200;
                return response()->json($token,200);
            }
        }
        return response()->json(['msg'=>'Unauthorized Token.'],401);
    }
}
