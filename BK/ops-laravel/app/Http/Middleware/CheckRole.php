<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as BaseTrimmer;

class CheckRole extends BaseTrimmer
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

        if( (Auth::user()->role=='M2') || (Auth::user()->role=='M3')) {
            if (isset($request->meetingid) || isset($request->id)) {
                $mid = $request->meetingid??$request->id;

                $res = WorkshopMeta::where('workshop_id', $request->workshop_id)->where('user_id', Auth::user()->id)->first();
                if (@$res->role != 1) {
                        return response()->json(['error' => 'Unauthenticated.'], 401);
                }

            }
        }
        return $next($request);
    }

}
