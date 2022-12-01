<?php

namespace Modules\KctUser\Http\Middleware;

use App\AccountSettings;
use Closure;
use Illuminate\Http\Request;
use Modules\KctUser\Services\KctService;

class KctEnableCheck {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        return $next($request);
    }
}
