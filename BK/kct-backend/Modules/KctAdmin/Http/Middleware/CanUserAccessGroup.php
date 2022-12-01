<?php

namespace Modules\KctAdmin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\KctUser\Traits\Services;

class CanUserAccessGroup
{
    use ServicesAndRepo;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        $sourceType = $this->findGroupKeySource($request);
        $groupKey = $sourceType == 'route' ? $request->route('groupKey') : $request->input('group_key');
        $group = $this->adminRepo()->groupRepository->findByGroupKey($groupKey);
        $currentGroup = $this->adminServices()->groupService->getUserCurrentGroup(Auth::id());
        if(!$group){
            return $next($request);
        }
        $group->load('admins');
        $allowedUsers = $group->admins->pluck('user_id')->toArray();
//        $allowedUsers = $this->getAllOwnerAndOrganiser($group);
        $grantAccess = in_array(Auth::user()->id, $allowedUsers);
        if (!$grantAccess) {
            $superGroup = $this->adminRepo()->groupRepository->findById(1);
            $allowedUsers = $superGroup->admins->pluck('user_id')->toArray();
            $grantAccess = in_array(Auth::user()->id, $allowedUsers);
        }
        if ($grantAccess) {
            return $next($request);
        }
        return response()->json([
            'status' => false,
            'msg' => __('kctadmin::messages.cannot_access_group'),
            'code' => config('kctadmin.api_custom_code.invalid_group'),
            'current_group_key' => $currentGroup->group_key
        ],
            403);
    }

    private function getAllOwnerAndOrganiser($group) {
        $organiser = $group->organiser->pluck('user_id');
        $owner = $group->owner->pluck('user_id');
        return $organiser->merge($owner)->toArray();
    }

    private function findGroupKeySource($request): string {
        return $request->route('groupKey') ? 'route' : 'request';
    }
}
