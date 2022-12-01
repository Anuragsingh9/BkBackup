<?php

namespace Modules\Messenger\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Messenger\Service\AuthorizationService;
use Modules\Messenger\Service\ChannelService;

class IsUserBelongToWorkshop {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        return AuthorizationService::getInstance()->checkUserBelongsToWorkshop($request->workshop_id)
            ? $next($request)
            : response()->json(['status' => FALSE, 'msg' => 'You are not authorized to access this'], 403);
    }
    
}
