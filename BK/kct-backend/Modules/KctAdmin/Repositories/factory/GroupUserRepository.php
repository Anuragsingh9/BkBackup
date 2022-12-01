<?php


namespace Modules\KctAdmin\Repositories\factory;


use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\KctAdmin\Entities\FavouriteGroup;
use Modules\KctAdmin\Entities\Group;
use Modules\KctAdmin\Entities\GroupUser;
use Modules\KctAdmin\Repositories\IGroupUserRepository;
use Modules\KctAdmin\Traits\ServicesAndRepo;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will contain the group user management related functionality
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class GroupUserRepository
 * @package Modules\KctAdmin\Repositories\factory
 */
class GroupUserRepository implements IGroupUserRepository {
    use ServicesAndRepo;

    /**
     * @inheritDoc
     */
    public function storeMultipleGroupUser($groupUser) {
        return GroupUser::insert($groupUser);
    }

    /**
     * @inheritDoc
     */
    public function addGroupUser($groupId, $userId, $groupRole = null) {
        if ($groupRole) {
            $data = [
                'group_id' => $groupId,
                'user_id'  => $userId,
                'role'     => $groupRole,
            ];
        } else {
            $data = [
                'group_id' => $groupId,
                'user_id'  => $userId,
            ];
        }
        return GroupUser::firstOrCreate($data);
    }

    /**
     * @inheritDoc
     */
    public function getGroupUsers($groupId, array $role) {
        return GroupUser::where('group_id', $groupId)->whereIn('role', $role)->first();
    }

    /**
     * @inheritDoc
     */
    public function removeGroupUser($id, $groupId) {
        return GroupUser::whereIn('user_id', $id)->whereGroupId($groupId)->delete();
    }

    /**
     * @inheritDoc
     */
    public function addUserAsPilot($groupId, $pilots) {
        if ($pilots) {
            foreach ($pilots as $pilot) {
                GroupUser::updateOrCreate(
                    ['group_id' => $groupId, 'user_id' => $pilot],
                    ['role' => GroupUser::$role_Organiser]
                );
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function updateUserFavGroups($groupId, $isFavGroup) {
        // if isFavGroup=1 then group will be added in user's favourite group list
        // if isFavGroup=0 then group will be removed from user's favourite group list
        return $isFavGroup ? FavouriteGroup::firstOrCreate(['user_id' => Auth::user()->id, 'group_id' => $groupId]) :
            FavouriteGroup::where('user_id', Auth::user()->id)->where('group_id', $groupId)->delete();
    }

    /**
     * @inheritDoc
     */
    public function isFavouriteGroup($groupId) {
        return FavouriteGroup::where('user_id', Auth::user()->id)->where('group_id', $groupId)->first();
    }

    /**
     * @inheritDoc
     */
    public function updateUserLastVisitedGroup($groupId) {
        return GroupUser::whereGroupId($groupId)->whereUserId(Auth::user()->id)
            ->update(['last_visit' => Carbon::now()]);
    }

    /**
     * @inheritDoc
     */
    public function getUserCurrentGroupId($userId) {
        $currentGroup = GroupUser::whereUserId($userId)->whereIn('role', [
            GroupUser::$role_Organiser,
            GroupUser::$role_owner,
            GroupUser::$role_co_pilot
        ])->orderBy('last_visit', 'desc')
            ->orderBy('created_at', 'desc')->whereHas('group');
        return $currentGroup->first();
    }

    /**
     * @inheritDoc
     */
    public function getCurrentUserGroups($userId) {
        return GroupUser::whereUserId($userId)->get();
    }

    /**
     * @inheritDoc
     */
    public function getUserFirstAddedGroupId($userId) {
        $firstGroup = GroupUser::whereUserId($userId)->whereIn('role', [
            GroupUser::$role_Organiser,
            GroupUser::$role_owner,
            GroupUser::$role_co_pilot
        ])->first();
        return $firstGroup ? $firstGroup->group_id : null;
    }

    /**
     * @inheritDoc
     */
    public function isUserPilotOrOwner(): bool {
        return (bool)GroupUser::where('user_id', Auth::id())->whereIn('role', [2, 3, 4])->count();
    }

    /**
     * @inheritDoc
     */
    public function getGroups() {
        return Group::whereHas('groupUser', function ($q) {
            $q->where('user_id', Auth::id());
        })->get();
    }

    /**
     * @inheritDoc
     */
    public function getUserGroupRole($groupId, $userId) {
        $groupUser = GroupUser::whereGroupId($groupId)->whereUserId($userId)->first();
        return $groupUser->role;
    }

    /**
     * @inheritDoc
     */
    public function isOrganiser($userId) {
        return GroupUser::where('user_id', $userId)->whereIn('role', [2, 3, 4])->count();
    }

    /**
     * @inheritDoc
     */
    public function addUserAsCoPilot($groupId, $coPilots) {
        if ($coPilots) {
            foreach ($coPilots as $coPilot) {
                GroupUser::updateOrCreate(
                    ['group_id' => $groupId, 'user_id' => $coPilot],
                    ['role' => GroupUser::$role_co_pilot]
                );
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function updateGroupFirstPilot($groupId, $pilot) {
        return GroupUser::updateOrCreate(
            ['group_id' => $groupId, 'role' => GroupUser::$role_Organiser],
            ['user_id' => $pilot]
        );
    }

    /**
     * @inheritDoc
     */
    public function isUserPartOfGroup($groupId) {
        $user = GroupUser::where('group_id', $groupId)->where('user_id', Auth::id())->first();
        return $user;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function isUserSuperPilotOrOwner() {
        $defaultGroup = $this->adminRepo()->groupRepository->getDefaultGroup();
        return GroupUser::where('group_id', $defaultGroup->id)->where('user_id', Auth::id())->whereIn('role', [2, 3, 4])->first();
    }

    /**
     * @inheritDoc
     */
    public function isUserMemberOfGroup($groupId, $userId) {
        return GroupUser::where('group_id', $groupId)->where('user_id', $userId)->first();
    }

    /**
     * @inheritDoc
     */
    public function isUserPilotOfGroup($groupId) {
        return GroupUser::where('group_id', $groupId)->where('user_id', Auth::id())->where('role', 2)->first();
    }

    /**
     * @inheritDoc
     */
    public function isUserCopilotOfGroup($groupId) {
        return GroupUser::where('group_id', $groupId)->where('user_id', Auth::id())->where('role', 4)->first();
    }

    /**
     * @inheritDoc
     */
    public function isUserOwnerOfGroup($groupId) {
        return GroupUser::where('group_id', $groupId)->where('user_id', Auth::id())->where('role', 3)->first();
    }

    /**
     * @inheritDoc
     */
    public function updateUserGroupRole($groupId, $role, $userId) {
        return GroupUser::updateOrCreate(
            ['group_id' => $groupId, 'user_id' => $userId],
            ['role' => $role]
        );
    }

    /**
     * @inheritDoc
     */
    public function getUserPilotGroups($userId) {
        return GroupUser::whereUserId($userId)->whereIn('role', [
            GroupUser::$role_Organiser,
            GroupUser::$role_owner,
            GroupUser::$role_co_pilot
        ])->get();
    }
}
