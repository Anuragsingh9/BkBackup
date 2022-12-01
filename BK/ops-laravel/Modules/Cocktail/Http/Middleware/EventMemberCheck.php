<?php

namespace Modules\Cocktail\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Cocktail\Entities\EventUser;
use Modules\Cocktail\Services\KctService;

class EventMemberCheck {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $eventIdSource
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $eventIdSource) {
        if ($eventIdSource == 'request') {
            $eventUuid = $request->input('event_uuid');
        } else {
            $eventUuid = $request->route('eventUuid');
        }
        $isEventUser = EventUser::with('event')
            ->where('event_uuid', $eventUuid)
            ->where('user_id', Auth::user()->id)
            ->first();
        if ($isEventUser) {
            return $next($request);
        } else {
            return response()->json(['status' => false, 'data' => __('cocktail::message.not_belongs_event')], 403);
        }
    }
}
