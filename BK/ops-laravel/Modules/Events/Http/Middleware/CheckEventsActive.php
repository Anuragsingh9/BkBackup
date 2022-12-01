<?php

namespace Modules\Events\Http\Middleware;

use App\AccountSettings;
use App\Setting;
use App\Workshop;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Events\Entities\EventMember;

class CheckEventsActive {
    public function __construct() {
        $this->tenancy = app(\Hyn\Tenancy\Environment::class);
    }
    
    
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $accountSetting = AccountSettings::where('account_id', $this->tenancy->hostname()['id'])->first(['setting']);
        if (!($accountSetting && $accountSetting->setting
            && isset($accountSetting->setting['event_enabled']) && $accountSetting->setting['event_enabled'])) {
            return response()->json(['error' => __('events::message.module_not_active')], 403);
        }
        return $next($request);
    }
}
