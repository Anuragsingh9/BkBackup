<?php

namespace App\Http\Middleware;

use App\WorkshopMeta;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckGenuine
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if( Auth::user()->role!='M1' ) {
            if( Auth::user()->role!='M0'){
                      if (isset($request->wid) || isset($request->id)) {
                        $wid = $request->wid??$request->id;
                        $workshopMeta = WorkshopMeta::where(['user_id' => Auth::user()->id, 'workshop_id' => $wid])->count();
                        if ($workshopMeta <= 0) {
                            return response()->json(['error' => 'Unauthenticated.'], 401);
                        }
                    }
            }
          
        }
        return $next($request);
    }
}
