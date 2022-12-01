<?php

namespace Modules\SuperAdmin\Http\Middleware;

use Closure;
use Hyn\Tenancy\Environment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnlyNonLoginMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        $tenant = app(Environment::class);
        if ($tenant->hostname()) {
            abort(404);
        }
        if (Auth::check()) {
            return redirect()->route('su-account-list');
        }
        return $next($request);
    }
}
