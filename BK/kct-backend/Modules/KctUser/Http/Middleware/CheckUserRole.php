<?php

namespace Modules\KctUser\Http\Middleware;

use App\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserRole {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        if (Auth::check()) {
            $setting = Setting::where('setting_key', 'event_settings')->first();
            $org1 = $org2 = null;
            if ($setting) {
                $decode = json_decode($setting->setting_value);
                $org2 = isset($decode->event_virtual_org_setting->default_organiser) ? $decode->event_virtual_org_setting->default_organiser : null;
                $org1 = isset($decode->event_org_setting->default_organiser) ? $decode->event_org_setting->default_organiser : null;
            }
            if (!(Auth::user()->role == 'M1' || Auth::user()->role == 'M0' || in_array(Auth::user()->id, [$org1, $org2]))) {
                return response()->json(['error' => __('cocktail::message.not_admin')], 401);
            }
        }
        return $next($request);
    }
}
