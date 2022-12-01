<?php

namespace Modules\SuperAdmin\Http\Middleware;

use Closure;
use Hyn\Tenancy\Environment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuAuth {
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        $tenant = app(Environment::class);
        if ($tenant->hostname()) {
            abort(404);
        }
        if (Auth::check()) {
            return $next($request);
        }
        return redirect()->route('su-signin');
    }
}
