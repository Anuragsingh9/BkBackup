<?php

namespace Modules\Crm\Http\Middleware;

use App\AccountSettings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TranscribeCheck {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        $this->tenancy = app(\Hyn\Tenancy\Environment::class);
        $msg = 'Sorry You Are Not Authorized for this action';
        $accountSetting = AccountSettings::where('account_id', $this->tenancy->hostname()['id'])->first();
        if(isset($accountSetting->setting['transcribe_setting'])) {
            $credit = $accountSetting->setting['transcribe_setting']['available_credit'];
            if($credit > 0) {
                return $next($request);
            } else {
                $msg = 'Credit Not Available';
            }
        }
        return response()->json(['status' => FALSE, 'msg' => $msg], 422);
    }
}
