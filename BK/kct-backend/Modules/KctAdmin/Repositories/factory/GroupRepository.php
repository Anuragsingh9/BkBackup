<?php


namespace Modules\KctAdmin\Repositories\factory;


use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\KctAdmin\Entities\Event;
use Modules\KctAdmin\Entities\Group;
use Modules\KctAdmin\Entities\GroupSetting;
use Modules\KctAdmin\Entities\GroupType;
use Modules\KctAdmin\Entities\GroupTypeRelation;
use Modules\KctAdmin\Entities\GroupUser;
use Modules\KctAdmin\Exceptions\DefaultGroupNotFoundException;
use Modules\KctAdmin\Repositories\IGroupRepository;
use Modules\KctAdmin\Traits\ServicesAndRepo;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will contain the group management related functionality
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class GroupRepository
 * @package Modules\KctAdmin\Repositories\factory
 */
class GroupRepository implements IGroupRepository {
    use ServicesAndRepo;

    /**
     * @inheritDoc
     */
    public function createGroup($param, $groupType, array $mainSetting = []): Group {
        // creating a group
        $group = Group::create($param);

        $mainSetting['allow_user'] = $mainSetting['allow_user'] ?? 0;
        $mainSetting['allow_manage_pilots_owner'] = $mainSetting['allow_manage_pilots_owner'] ?? 0;
        $mainSetting['allow_design_setting'] = $mainSetting['allow_design_setting'] ?? 0;
        $mainSetting['type_value'] = $mainSetting['type_value'] ?? null;

        // setting the data for group setting for "main_setting" key
        $this->adminRepo()->settingRepository->setSetting(
            $group->id,
            'main_setting',
            $mainSetting
        );

        // searching for group type by name to get id
        $groupType = GroupType::where('group_type', $groupType)->first();

        if (!$groupType) {
            throw new Exception('Invalid group type for creating group');
        }

        // creating group id and group type id relation to assign a type to group
        GroupTypeRelation::create([
            'group_id' => $group->id,
            'type_id'  => $groupType->id,
        ]);
        return $group;
    }

    /**
     * @inheritDoc
     */
    public function storeGroupUser($param): GroupUser {
        return GroupUser::create($param);
    }


    /**
     * @inheritDoc
     */
    public function getDefaultGroup($exception = true): ?Group {
        $group = Group::first();
        if (!$group && $exception) {
            throw new DefaultGroupNotFoundException('Default Group Not Found');
        }
        return $group;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is used to add group organisers(Pilot, owner)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $param
     * @return mixed
     */
    public function addGroupOrganiser($param) {
        return GroupUser::create($param);
    }

    /**
     * @inheritDoc
     */
    public function findById($id): ?Group {
        return Group::find($id);
    }

    /**
     * @inheritDoc
     */
    public function findGroupById($id) {
        return Group::find($id);
    }

    /**
     * @inheritDoc
     */
    public function updateGroup($param, $group): Group {
        // Update group data
        $group->update($param['group']);
        // Update group settings
        $this->adminRepo()->settingRepository->setSetting(
            $group->id,
            'main_setting',
            $param['main_settings']
        );
        // Update the group type
        GroupTypeRelation::where('group_id', $group->id)->update(['type_id' => $param['group_type_id']]);

        //Update group pilots
        if ($param['group_pilots']) {
            //fetch the existing pilots
            $pilot = GroupUser::where('group_id', $group->id)->where('role', 2)->get();

            //compare the new and existing pilots
            $deletePilots = array_values(array_diff($pilot->pluck('user_id')->toArray(), $param['group_pilots']));
            $updatePilots = array_values(array_diff($param['group_pilots'], $pilot->pluck('user_id')->toArray()));

            // change the pilots role (2 to 1)
            GroupUser::where('group_id', $group->id)->where('user_id', $deletePilots)->update(['role' => 1]);

            //update the pilots
            $this->adminRepo()->groupUserRepository->addUserAsPilot($group->id, $updatePilots);
        }
        return $group;
    }

    /**
     * @inheritDoc
     */
    public function updateGroupKey($group, $groupKey): group {
        $group->group_key = $groupKey;
        $group->update();
        return $group;
    }

    /**
     * @inheritDoc
     */
    public function getAllGroups() {
        return Group::whereHas('groupUser', function ($q) {
            $q->where('user_id', Auth::user()->id);
        })->get();
    }

    /**
     * @inheritDoc
     */
    public function getCurrentUserGroups($order, $orderBy, $isPaginated = false, ?string $key = null, $limit = 10, $groupType, $filter = null) {
        $date = Carbon::now();
        $superAdmins = $this->adminServices()->superAdminService->getAllSuperAdmins();
        $superAdminsEmail = $superAdmins->pluck('email')->toArray();
        if ($orderBy == 'next_event') {
            // Here next_event key is used for checking if sorting by next event is requested
            $results = Event::with(['group' => function ($q) {
                $q->with('mainSetting');
            }, 'group.pilots', 'group.groupType'])
                ->whereHas('group.groupUser', function ($q) {
                    $q->where('user_id', Auth::id());
                    $q->whereIn('role', [2, 3, 4]);
                })->whereHas('draft', function ($j) {
                    $j->where('event_status', 1);
                })->where('end_time', ">", $date->toDateTimeString())
                ->orderBy('start_time', $order)->get();
            $result = $results->pluck('group')->unique('id');
            return $result;
        } else {
            if (in_array(Auth::user()->email, $superAdminsEmail)) {
                $result = Group::with(['events' => function ($q) use ($date) {
                        $q->whereHas('draft', function ($j) {
                            $j->where('event_status', 1);
                        });
                        $q->where('end_time', ">", $date->toDateTimeString());
                        $q->orderBy('start_time', 'desc');
                    }
                        , 'mainSetting']
                )->where(function ($q) use ($key) {
                    if ($key)
                        $q->where('name', 'like', "%$key%");
                })->whereHas('groupType', function ($q) use ($groupType) {
                    if ($groupType)
                        $q->whereIn('group_type', $groupType);
                })->orderBy($orderBy, $order)
                    ->limit($limit);
            } else {
                $defaultGroup = Group::with(['groupUser' => function ($q) {
                    $q->where('user_id', Auth::user()->id);
                    $q->whereIn('role', [2, 3, 4]);
                }])->first();
                if ($defaultGroup && $defaultGroup->groupUser->count()) {
                    $isUserSuper = true;
                } else {
                    $isUserSuper = false;
                }

                $result = Group::with(['events' => function ($q) use ($date) {
                    $q->whereHas('draft', function ($j) {
                        $j->where('event_status', 1);
                    });
                    $q->where('end_time', ">", $date->toDateTimeString());
                    $q->orderBy('start_time', 'desc');
                }, 'groupUser', 'isFavGroup'    => function ($q) {
                    $q->where('user_id', Auth::id());
                }
                ])->whereHas('groupUser', function ($q) use ($filter, $isUserSuper) {
                    if (!$isUserSuper) {
                        $q->where('user_id', Auth::user()->id)
                            ->whereIn('role', [
                                GroupUser::$role_Organiser, GroupUser::$role_owner, GroupUser::$role_co_pilot
                            ]);
                    }
                    if ($filter == 'pilot') {
                        $q->whereIn('role', [2, 3, 4]);
                    }
                })->where(function ($q) use ($key) {
                    if ($key)
                        $q->where('name', 'like', "%$key%");
                })->whereHas('groupType', function ($q) use ($groupType) {
                    if ($groupType)
                        $q->whereIn('group_type', $groupType);
                })->orderBy($orderBy, $order)
                    ->limit($limit);
            }
        }

        // if FR request want to paginated data
        if ($isPaginated) {
            return $result->paginate($limit);
        }
        return $result->get();
    }

    /**
     * @inheritDoc
     */
    public function getAccountSettings() {
        return groupSetting::where('setting_key', 'account_settings')->first();
    }

    /**
     * @inheritDoc
     */
    public function countGroupUsers($id) {
        return GroupUser::where('group_id', $id)->count(DB::raw('DISTINCT user_id'));
    }

    /**
     * @inheritDoc
     */
    public function getGrpOrganiser($id) {
        return GroupUser::where('group_id', $id)->where('role', 1)->pluck('user_id');
    }

    /**
     * @inheritDoc
     */
    public function getUser($groupOrgId) {
        return User::whereIn('id', $groupOrgId)->get(); // temporarily for testing I have used user repository in Kctadmin
    }

    /**
     * @inheritDoc
     */
    public function addUser(int $userId, int $groupId, int $role) {
        return GroupUser::create([
            'group_id' => $groupId,
            'user_id'  => $userId,
            'role'     => $role,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getGroupUsers(string $groupKey, ?int $role = 1, ?string $orderBy = 'lname', ?string $order = 'asc'): \Illuminate\Support\Collection {
        return User::whereHas('group', function ($q) use ($groupKey, $role) {
            $q->whereGroupKey($groupKey);
            $q->whereRole($role);
        })
            ->with(['group', 'company', 'unions'])
            ->orderBy($orderBy, $order)
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getGroupUserRelation($groupId) {
        return GroupUser::where('group_id', $groupId)->get();
    }

    /**
     * @inheritDoc
     */
    public function getGroupUserRole(int $groupId, array $type, $isPaginated = false, $regType = 1) {
        $su = $this->adminServices()->superAdminService->getAllSuperAdmins();
        $suEmails = $su->pluck('email')->toArray();
        $suIds = $this->adminServices()->userService->getUsersByEmail($suEmails)->pluck('id')->toArray();
        $userData = GroupUser::with('user', 'user.company', 'user.unions')
            ->where('group_id', $groupId)
            ->whereHas('user', function ($q) use ($regType) {
                if ($regType !== 0) {
                    $q->whereHas('userMeta', function ($q) use ($regType) {
                        $q->where('signup_type', $regType);
                    });
                    if ($regType == 1) {
                        $q->orWhereDoesntHave('userMeta');
                    }
                }
            })
            ->whereIn('role', $type)
            ->whereNotIn('user_id', $suIds);
        return $userData->get();
    }

    /**
     * @inheritDoc
     */
    public function getGroupOrganizers(int $groupId, ?string $orderBy = null): \Illuminate\Support\Collection {
        $builder = User::whereHas('group', function ($q) use ($groupId) {
            $q->where('group_id', $groupId);
            $q->where('role', GroupUser::$role_Organiser);
        });
        if ($orderBy) {
            if ($orderBy == 'name') {
                return $builder->orderBy("fname")->orderBy("lname")->get();
            } else if ($orderBy == 'id') {
                return $builder->orderBy("id")->get();
            }
        }
        return $builder->get();
    }

    /**
     * @inheritDoc
     */
    public function getGroupIdByGroupKey($groupKey) {
        return Group::where('group_key', $groupKey)->first();
    }

    /**
     * @inheritDoc
     */
    public function findByGroupKey($groupKey) {
        return Group::whereGroupKey($groupKey)->first();
    }

    /**
     * @inheritDoc
     */
    public function getGroupIdByKey($groupKey) {
        $group = $this->findByGroupKey($groupKey);
        return $group->id;
    }

    /**
     * @inheritDoc
     */
    public function getGroupByGroupKey($groupKey): ?Group {
        return Group::where('group_key', $groupKey)->first();
    }

    /**
     * @inheritDoc
     */
    public function createGroupTypeRelation($groupId, $groupTypeId) {
        return GroupTypeRelation::create([
            'group_id' => $groupId,
            'type_id'  => $groupTypeId
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getGroupTypeId($groupType) {
        return GroupType::whereGroupType($groupType)->first()->id;
    }

    /**
     * @inheritDoc
     */
    public function fetchAllGroups() {
        return Group::all();
    }

    /**
     * @inheritDoc
     */
    public function getGroupFirstPilot($groupId) {
        $group = Group::find($groupId);
        $firstPilot = $group->organiser->first()->load('user');
        return $firstPilot->user;
    }

    /**
     * @inheritDoc
     */
    public function getGroupAllEvents($group): int {
        $groupEvents = $group->load('events');
        return count($groupEvents->events);
    }

    /**
     * @inheritDoc
     */
    public function getGroupAllDraftEvents($group, $draftType): int {
        $publishedEvent = $group->load(['events' => function ($q) use ($draftType) {
            $q->whereHas('draft', function ($q) use ($draftType) {
                $q->where('event_status', $draftType);
            });
        }]);
        return count($publishedEvent->events);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function isSuperPilotOrOwner(): bool {
        $group = $this->getDefaultGroup();
        $group->load(['groupUser' => function ($q) {
            $q->where('user_id', Auth::id());
            $q->whereIn('role', [GroupUser::$role_Organiser, GroupUser::$role_owner, GroupUser::$role_co_pilot]);
        }]);
        return (bool)$group->groupUser->count();
    }

    /**
     * @inheritDoc
     */
    public function getGroupByIds($ids) {
        return Group::whereIn('id', $ids)->get();
    }

    /**
     * @inheritDoc
     */
    public function syncGroupMainSetting() {

        $allGroups = Group::all();
        foreach ($allGroups as $group) {
            $setting = GroupSetting::firstOrCreate(
                [
                    'setting_key' => 'main_setting',
                    'group_id'    => $group->id,
                ],
                [
                    'setting_value' => [],
                ]
            );

            $values = $setting->setting_value;
            $values['allow_user'] = $values['allow_user'] ?? 0;
            $values['allow_manage_pilots_owner'] = $values['allow_manage_pilots_owner'] ?? 0;
            $values['allow_design_setting'] = $values['allow_design_setting'] ?? 0;

            $setting->setting_value = $values;
            $setting->update();
        }
    }

    /**
     * @inheritDoc
     */
    public function getGroupsByGroupKeys($keys) {
        return Group::whereIn('group_key', $keys)->get();
    }
}
