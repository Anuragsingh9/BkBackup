<?php

namespace App\Http\Middleware;

use Closure;

class CheckCrmActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $permissions = \Auth::user()->permissions;
        $crmAdmin = (isset($permissions['crmAdmin']) && $permissions['crmAdmin'] == 1) ?? 0;
        $crmEditor = (isset($permissions['crmEditor']) && $permissions['crmEditor'] == 1) ?? 0;
        $crmAssistance = (isset($permissions['crmAssistance']) && $permissions['crmAssistance'] == 1) ?? 0;
        $crmRecruitment = (isset($permissions['crmRecruitment']) && $permissions['crmRecruitment'] == 1) ?? 0;

        $hostname = app(\Hyn\Tenancy\Environment::class)->hostname();
        $superPermission = \DB::connection('mysql')->table('account_settings')->where('account_id', $hostname->id)->first(['crm_menu_enable']);

        if (isset($superPermission->crm_menu_enable) && $superPermission->crm_menu_enable == 1) {
            if ($crmAdmin || $crmEditor || $crmAssistance || $crmRecruitment || \Auth::user()->role == 'M1' || \Auth::user()->role == 'M0') {
                return $next($request);
            } else {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
        }

        return $next($request);
    }
}
