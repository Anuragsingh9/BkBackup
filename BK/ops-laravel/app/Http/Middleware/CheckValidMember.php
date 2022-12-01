<?php

namespace App\Http\Middleware;
use App\Meeting;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckValidMember
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
                $meetingPresence = Meeting::join('workshops', function ($join) {
                    $join->on('meetings.workshop_id', '=', 'workshops.id');
                })->join('workshop_metas', function ($joins) {
                    $joins->on('workshops.id', '=', 'workshop_metas.workshop_id')->where('workshop_metas.user_id',Auth::user()->id);
                })->where(['meetings.id' => $mid])->count();
               // var_dump($meetingPresence,Auth::user());exit;
                if ($meetingPresence <= 0) {
                    return response()->json(['error' => 'Unauthenticated.'], 401);
                }
            }
        }
        return $next($request);
    }

}
