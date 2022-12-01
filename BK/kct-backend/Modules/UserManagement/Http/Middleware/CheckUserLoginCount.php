<?php

namespace Modules\UserManagement\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserLoginCount
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(!(Auth::check())){
            return $next($request);
        }
        else{
            if((Auth::user()->login_count) == 0){
                return response()->json([
                    'status' => false,
                    'code' => 1001
                ],
                403);
            }
            return $next($request);
        }
    }
}
