<?php

namespace Modules\KctUser\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\KctUser\Services\KctCoreService;

class CheckUserBanMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @param $severity
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $severity) {
        if ($user = $request->user('api')) {
            if ($severity == 'event-route') {
                $eventUuid = $request->route('eventUuid');
                $isBanned = KctCoreService::getInstance()->getBannedUser($user->id, $eventUuid);
                if ($isBanned) {
                    $redirectUrl = $isBanned->severity == 3
                        ? KctCoreService::getInstance()->getRedirectUrl($request, 'quick-login', ['/EVENT_UUID' => ''])
                        : KctCoreService::getInstance()->getRedirectUrl($request, 'event-list');
                    return response([
                        'status'       => false,
                        'msg'          => __("cocktail::message.banned_event"),
                        'redirect_url' => $redirectUrl,
                    ], 403);
                }
            }
        }
        return $next($request);
    }
}
