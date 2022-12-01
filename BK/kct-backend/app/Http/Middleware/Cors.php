<?php

namespace App\Http\Middleware;

use Closure;

class Cors {
    public function handle($request, Closure $next) {
        return $next($request)
            ->header('Access-Control-Allow-Origin', $request->header('origin'))
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header('Access-Control-Allow-Headers', '*')
            ->header('Access-Control-Allow-Methods', '*')
            ->header('X-Requested-With', 'XMLHttpRequest');
    }
}
