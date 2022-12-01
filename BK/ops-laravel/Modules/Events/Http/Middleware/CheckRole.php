<?php

namespace Modules\Events\Http\Middleware;

use App\Setting;
use Closure;
use Auth;
use Illuminate\Http\Request;
use Modules\Events\Service\EventService;

class CheckRole {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role) {
        if (Auth::check()) {
            if ($role == 'admin') {
                if(!EventService::getInstance()->isAdmin()) {
                    return response()->json(['error' => __('events::message.admin_only')], 401);
                }
            } else if ($role == 'sec') {
                if (Auth::user()->role_commision != 1  && !EventService::getInstance()->isAdmin()) {
                    return response()->json(['error' => __('events::message.admin_only')], 401);
                }
            }
        }
        return $next($request);
    }
}
