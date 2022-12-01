<?php

namespace Modules\Crm\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssistanceResource {
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
        $isCrmAssistance = ((isset($permissions->crmAssistance) && $permissions->crmAssistance) ? TRUE : FALSE);
        $msg = 'Sorry You Are Not Authorized for this action';
//        $isCrmRecruitment = ((isset($permissions->crmRecruitment) && $permissions->crmRecruitment) ? TRUE : FALSE);
        if (array_search($role, ['M1', 'M0'])) {
            return $next($request);
        } else if ($isCrmAdmin || $isCrmEditor) {
            return $next($request);
        } else if ($isCrmAssistance) {
            if ($request->method() == 'POST') { // ADDING
                if ($request->field_id == Auth::user()->id) {
                    return $next($request);
                }
                $msg = 'You can create assistance for you only';
            } else if ($request->method() == 'PUT' || $request->method() == 'DELETE') { // UPDATING
                $user = Auth::user()->with(['assistance_reports' => function ($q) use ($request) {
                    $q->where('id', $request->route('report'));
                }])->first();
                if($user->assistance_reports->count()) {
                    return $next($request);
                }
                $msg = 'You can\'t '. ($request->method()=='PUT' ? 'update' : 'delete' ) .' assistance which does not belongs to you';
            }
        }
        return response()->json(['status' => FALSE, 'msg' => $msg,], 422);
    }
}
