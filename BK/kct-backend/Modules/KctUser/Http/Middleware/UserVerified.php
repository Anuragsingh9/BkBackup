<?php

namespace Modules\KctUser\Http\Middleware;

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
                return response()->json([
                    'status' => false,
                    'msg' => __('kctuser::message.email_verify'),
                    'redirect_code' => config('kctadmin.api_custom_code.user_email_not_verified'),
                    'user' => [
                        'fname' => Auth::user()->fname,
                        'lname' => Auth::user()->lname,
                        'email' => Auth::user()->email,
                    ]
                ],
                    403);
            }
        }
        return $next($request);
    }
}
