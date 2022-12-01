<?php

namespace App\Http\Middleware;

use App\Meeting;
use App\WorkshopMeta;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAllowedRepd
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
        if( Auth::user()->role!='M1') {
            if( Auth::user()->role!='M0') {
                if (isset($request->mid) || isset($request->id)) {
                    $mid = $request->mid??$request->id;
                    $type = $request->type??'';
                    $meeting = Meeting::find($mid);
                    $res = WorkshopMeta::where('workshop_id', $meeting->workshop_id)->where('user_id', Auth::user()->id)->first();
                    if (@$res->role == 0) {
                        $validType = 'validated_' . $type;
                        if ($meeting->$validType == 0)
                            return response()->json(['error' => 'Unauthenticated.'], 401);
                    }
                }
            }
        }
        return $next($request);
    }
}
