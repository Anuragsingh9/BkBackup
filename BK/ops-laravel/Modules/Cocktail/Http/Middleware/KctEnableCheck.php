<?php

namespace Modules\Cocktail\Http\Middleware;

use App\AccountSettings;
use Closure;
use Illuminate\Http\Request;
use Modules\Cocktail\Services\KctService;

class KctEnableCheck {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        $setting = KctService::getInstance()->getAccountSetting();
        if (isset($setting['event_enabled'])
            && $setting['event_enabled']
            && isset($setting['event_settings']['keep_contact_enable'])
            && $setting['event_settings']['keep_contact_enable']) {
            return $next($request);
        }
        return response()->json(['status' => false, 'msg' => __('cocktail::message.module_not_active')], 403);
    }
}
