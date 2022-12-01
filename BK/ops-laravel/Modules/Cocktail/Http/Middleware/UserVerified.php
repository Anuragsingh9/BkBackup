<?php

namespace Modules\Cocktail\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserVerified {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        if (Auth::check()) {
            if (!Auth::user()->on_off) {
                return response()->json(['status' => false, 'msg' => __('cocktail::message.email_verify')], 403);
            }
        }
        return $next($request);
    }
}
