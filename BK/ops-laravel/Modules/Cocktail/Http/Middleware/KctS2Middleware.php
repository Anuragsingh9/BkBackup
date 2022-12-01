<?php

namespace Modules\Cocktail\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class KctS2Middleware {
   
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        return $next($request);
    }
}
