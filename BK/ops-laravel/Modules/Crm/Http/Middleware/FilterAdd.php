<?php

namespace Modules\Crm\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Crm\Entities\CrmFilter;

class FilterAdd {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        if(!Auth::check()){
            return response()->json(['status' => FALSE, 'msg' => 'Sorry You Are Not Authorized for this action'], 401);
        }
        $role = Auth::user()->role;
        $permissions = (object)Auth::user()->permissions;
        $isCrmAdmin = ((isset($permissions->crmAdmin) && $permissions->crmAdmin) ? TRUE : FALSE);
        $isCrmEditor = ((isset($permissions->crmEditor) && $permissions->crmEditor) ? TRUE : FALSE);
        $isCrmAssistance = ((isset($permissions->crmAssistance) && $permissions->crmAssistance) ? TRUE : FALSE);
        $isCrmRecruitment = ((isset($permissions->crmRecruitment) && $permissions->crmRecruitment) ? TRUE : FALSE);
        if (array_search($role, ['M1', 'M0'])) {
            return $next($request);
        } else if ($isCrmAdmin || $isCrmEditor || $isCrmAssistance || $isCrmRecruitment) {
            return $next($request);
        }
        return response()->json(['status' => FALSE, 'msg' => 'Sorry You Are Not Authorized for this action'], 422);

    }
}
