<?php

namespace Modules\SuperAdmin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class InSignUpProcessMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        if (session()->has('signup_email')) {
            return $next($request);
        }
        return redirect()->route('su-account-create-1');
    }
}
