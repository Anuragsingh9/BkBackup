<?php

namespace App\Http\Middleware;

use Closure;

class IcontactCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $hostname = app(\Hyn\Tenancy\Environment::class)->hostname();
        $setting = \App\AccountSettings::where('account_id', $hostname->id)->first(['setting']);
        // dd($setting);
        if(isset($setting->setting['ICONTACT_API_APP_ID']) && !empty($setting->setting['ICONTACT_API_APP_ID'])){
            return $next($request);
        }
        return response()->json(['status'=>false,'msg'=>'please setup icontact account in account'], 400);
        // return $next($request);

    }
}
