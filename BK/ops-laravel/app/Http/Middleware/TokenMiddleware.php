<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
use DB;
class TokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::check()){
                return $next($request);
        }
        
        return response()->json(['msg'=>'Unauthorized Token.'], 401);
        // if($request->cookie('session_id')){
        //     if(DB::table('tokens')->where('api_token',$request->cookie('session_id'))
        //     ->where('expired','>',date("Y-m-d H:i:s"))
        //     ->first())
        //     return $next($request);
        //     }
        // return response()->json(['msg'=>'Unauthorized Token.'], 401);
        //return $next($request);
    }
}
