<?php

namespace Modules\Cocktail\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Cocktail\Services\AuthorizationService;

class CheckUserIsEventAdminMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $from) {
        if ($from == 'route') {
            $event = $request->route('event_uuid');
        } else {
            $event = $request->input('event_uuid');
        }
        if (!AuthorizationService::getInstance()->isUserEventAdmin($event)) {
            return response()->json(['status' => false, 'msg' => __('cocktail::message.not_admin')], 403);
        }
        return $next($request);
    }
}
