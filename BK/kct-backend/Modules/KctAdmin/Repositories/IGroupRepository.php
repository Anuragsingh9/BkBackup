<?php

namespace Modules\KctAdmin\Repositories;

use Exception;
use Illuminate\Support\Collection;
use Modules\KctAdmin\Entities\Group;
use Modules\KctAdmin\Entities\GroupSetting;
use Modules\KctAdmin\Entities\GroupUser;
use Modules\KctAdmin\Exceptions\DefaultGroupNotFoundException;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This interface will contain the group management related functionality
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IGroupRepository
 * @package Modules\KctAdmin\Repositories
 */
interface IGroupRepository {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will create a group
     * 1. This method will create a group in groups table
     * 2. Then it will set values from parameter to group settings for "main_setting" key
     * 3. As every group must have a type so creating a type given in parameter
     *        then it will search that group type and add that type to group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $param
     * @param $groupType
     * @param array $mainSetting
     * @return mixed
     */
    public function createGroup($param, $groupType, array $mainSetting = []): Group;


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To store Group user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $param
     * @return mixed
     */
    public function storeGroupUser($param): GroupUser;


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the account default group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return Group
     * @throws DefaultGroupNotFoundException
     */
    public function getDefaultGroup($exception = true): ?Group;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to find the group by id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $id
     * @return Group|null
     */
    public function findById($id): ?Group;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is used to update the group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $param
     * @param $group
     * @return Group
     */
    public function updateGroup($param, $group): Group;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the group key
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $group
     * @param $groupKey
     * @return Group
     */
    public function updateGroupKey($group, $groupKey): Group;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To find the group by group id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $id
     * @return mixed
     */
    public function findGroupById($id);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the all groups
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function getAllGroups();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the current user group
     * get data is paginated and limited data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $order
     * @param $orderBy
     * @param false $isPaginated
     * @param string|null $key
     * @param int $limit
     * @param $groupType
     * @param null $filter
     * @return mixed
     */
    public function getCurrentUserGroups($order, $orderBy, $isPaginated = false, ?string $key = null, $limit = 50, $groupType, $filter = null);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the account settings of the group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function getAccountSettings();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @@descripiton This method will be used to count group users
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $id
     * @return mixed
     */
    public function countGroupUsers($id);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the group users
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $id
     * @return mixed
     */
    public function getGrpOrganiser($id);


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get a group user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupOrgId
     * @return mixed
     */
    public function getUser($groupOrgId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To add a user in group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $userId
     * @param int $groupId
     * @param int $role
     * @return mixed
     */
    public function addUser(int $userId, int $groupId, int $role);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the group users by their group role
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $groupKey
     * @param int|null $role
     * @param string|null $orderBy
     * @param string|null $order
     * @return Collection
     */
    public function getGroupUsers(string $groupKey, ?int $role = 1, ?string $orderBy = 'lname', ?string $order = 'asc'): Collection;

    /**
     * ------------------------------------------------------------------------------------------------------------------
     * @descripiton To get the all group user
     * ------------------------------------------------------------------------------------------------------------------
     *
     * @param $groupId
     * @return mixed
     */
    public function getGroupUserRelation($groupId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To fetch group users data according to the requested role
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $groupId
     * @param array $type
     * @param false $isPaginated
     * @param $reqFilter
     * @return mixed
     */
    public function getGroupUserRole(int $groupId, array $type, $isPaginated = false, $reqFilter);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the group organiser(pilot, owners)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $groupId
     * @param string|null $orderBy
     * @return Collection
     */
    public function getGroupOrganizers(int $groupId, ?string $orderBy = null): Collection;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get group id by group key
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupKey
     * @return mixed
     */
    public function getGroupIdByGroupKey($groupKey);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will be used to find the group by groupkey
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupKey
     * @return mixed
     */
    public function findByGroupKey($groupKey);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the group Id from group key
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupKey
     * @return mixed
     */
    public function getGroupIdByKey($groupKey);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the group model by group key
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupKey
     * @return Group|null
     */
    public function getGroupByGroupKey($groupKey): ?Group;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is used to create group type relation with the help of group id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupId
     * @param $groupTypeId
     * @return mixed
     */
    public function createGroupTypeRelation($groupId, $groupTypeId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is used to get group type id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupType
     * @return mixed
     */
    public function getGroupTypeId($groupType);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get all groups
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function fetchAllGroups();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description It will find the group first pilot and return the pilot data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupId
     * @return mixed
     */
    public function getGroupFirstPilot($groupId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get all the events of a group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $group
     * @return mixed
     */
    public function getGroupAllEvents($group);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get all the events related to draft of a group according to the type given
     *  $draftType = 1,2 (1 = published, 2 = draft)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $group
     * @param $draftType
     * @return mixed
     */
    public function getGroupAllDraftEvents($group, $draftType);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To check if the current user have role of super pilot,super owner or super co-pilot
     *
     * @note Super word is used here which means user have above-mentioned role in default group.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function isSuperPilotOrOwner();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get groups by group ids
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $ids
     * @return mixed
     */
    public function getGroupByIds($ids);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is used to synchronize the main setting of the groups
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function syncGroupMainSetting();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is used for get groups by group key
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $keys
     * @return mixed
     */
    public function getGroupsByGroupKeys($keys);

}
