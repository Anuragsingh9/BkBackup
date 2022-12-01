<?php

namespace App\Http\Middleware;

use App\EntityType;
use App\Services\AppService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetLocaleMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
//        dd("we are here to set locale", EntityType::all());
        AppService::setUserLocale();
        return $next($request);
    }
}
