<?php

namespace Modules\KctUser\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class KctNodeCorsMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        return $next($request)
            ->header('Access-Control-Allow-Origin', $request->header('origin'))
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header('Access-Control-Allow-Headers', 'Content-Type,User-Id,API-Token,Origin,X-XSRF-TOKEN,ops_session,Accept,Authorization,authorization,X-Auth-Token')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE,OPTIONS')
            ->header('X-Requested-With', 'XMLHttpRequest');
    }
}
