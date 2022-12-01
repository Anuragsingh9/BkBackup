<?php

namespace Modules\KctUser\Http\Middleware;

use App;
use Closure;
use Illuminate\Http\Request;
use Modules\KctUser\Services\KctService;

class SetKctLocaleMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        App::setLocale(KctService::getInstance()->getCurrentLang());
        return $next($request);
    }
}
