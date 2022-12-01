<?php

namespace Modules\SuperAdmin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RootApiCorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->getHost() === env('MAIN_DOMAIN')){
            return $next($request)
                ->header('Access-Control-Allow-Origin', $request->header('origin'))
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Access-Control-Allow-Headers', '*')
                ->header('Access-Control-Allow-Methods', '*')
                ->header('X-Requested-With', 'XMLHttpRequest');
        }
        $redirectUrl = env('HOST_TYPE') . env('MAIN_DOMAIN');
        return redirect()->to($redirectUrl);
    }
}
