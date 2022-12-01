<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Http\Request;
use App\Token;
use Session;
class MobileApi
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
        
        if($request->header('id') || Auth::check()){
           if ( Auth::check() ) {
                $user = Auth::user();
             }
             else{
                Auth::loginUsingId($request->header('id'));
             }
            return $next($request);
           
          }
        return response()->json(['status'=>401,'msg'=>'Unauthorized Token.'], 401);
        //$request->header('API-Token')
    }
}
