<?php

namespace Modules\KctUser\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\KctUser\Traits\Repo;

class UserEmailVerifiedMiddleware {

    use Repo;
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        if (Auth::check()) {
            if (!Auth::user()->email_verified_at) {
                $data = [
                    'status'        => false,
                    'msg'           => __('kctuser::message.email_verify'),
                    'redirect_code' => config('kctadmin.api_custom_code.user_email_not_verified'),
                    'user'          => [
                        'fname' => Auth::user()->fname,
                        'lname' => Auth::user()->lname,
                        'email' => Auth::user()->email,
                    ]
                ];
                $events = $this->userRepo()->userRepository->getUserEvents(Auth::user()->id);
                if($events->count()) {
                    $data['last_event_uuid'] = $events[0]->event_uuid;
                }
                return response()->json($data,
                    403);
            }
        }
        return $next($request);
    }
}
