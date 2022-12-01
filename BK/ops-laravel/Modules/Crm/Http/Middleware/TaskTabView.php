<?php

namespace Modules\Crm\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskTabView {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        $role = Auth::user()->role;
        $permissions = (object)Auth::user()->permissions;
        $isCrmAdmin = ((isset($permissions->crmAdmin) && $permissions->crmAdmin) ? TRUE : FALSE);
        $isCrmEditor = ((isset($permissions->crmEditor) && $permissions->crmEditor) ? TRUE : FALSE);
        $isCrmRecruitment = ((isset($permissions->crmRecruitment) && $permissions->crmRecruitment) ? TRUE : FALSE);
        $isCrmAssistance = ((isset($permissions->crmAssistance) && $permissions->crmAssistance) ? TRUE : FALSE);
        if (array_search($role, ['M1', 'M0'])) {
            return $next($request);
        } elseif ($isCrmAdmin || $isCrmEditor || $isCrmRecruitment || $isCrmAssistance) {
            return $next($request);
        }
        return response()->json(['status' => FALSE, 'msg' => 'Sorry You Are Not Authorized for this action'], 422);
    }
}
