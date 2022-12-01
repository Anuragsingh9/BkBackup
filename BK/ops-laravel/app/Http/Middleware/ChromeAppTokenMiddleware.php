<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Token;
class ChromeAppTokenMiddleware
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
        if($request->header('Api-Token')){
          if(Token::where('api_token',$request->header('Api-Token'))
            ->where('user_id',$request->header('user-Id'))
            //->where('expired','>',date("Y-m-d H:i:s"))
            ->first())
            return $next($request);
          }
        return response()->json(['status'=>401,'msg'=>'Unauthorized Token.'], 401);
        //$request->header('API-Token')
    }
}
