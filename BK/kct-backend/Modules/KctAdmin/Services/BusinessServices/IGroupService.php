<?php

namespace Modules\KctAdmin\Services\BusinessServices;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Modules\KctAdmin\Entities\Group;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This interface will contain the group management related functionality
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IGroupService
 * @package Modules\KctAdmin\Services\BusinessServices
 */
interface IGroupService {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create a group with default settings applied
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param array $data
     * @param $groupType
     * @return mixed
     * @throws Exception
     */
    public function createGroup(array $data, $groupType);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To set the group logo
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $groupId
     * @param UploadedFile|null $logo
     * @return mixed
     */
    public function setGroupLogo(int $groupId, ?UploadedFile $logo = null);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To sync the group settings to find/add if any settings is not present in settings table or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $groupId
     * @return int
     */
    public function syncGroupSettings(int $groupId): int;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the groupKey
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Group $group
     * @return mixed
     */
    public function prepareGroupKey(Group $group);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This will search for the missing keys in db related to broadcasting and put there default value if
     * not created already
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $groupId
     * @param Collection|null $keys
     * @return mixed
     */
    public function syncBroadcastingSettings(int $groupId, ?Collection $keys = null);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the array data set keys for the group settings
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $groupId
     * @param $value
     * @param $field
     * @return array
     * @deprecated
     */
    public function updateArraySettings(int $groupId, $value, $field): array;

//    /**
//     * -----------------------------------------------------------------------------------------------------------------
//     * @description To set the specific group main colors
//     * -----------------------------------------------------------------------------------------------------------------
//     *
//     * @param int $groupId
//     * @param array $groupMainColors
//     * @return mixed
//     * @throws Exception
//     */
//    public function setGroupMainColors(int $groupId, array $groupMainColors);
//
//    /**
//     * -----------------------------------------------------------------------------------------------------------------
//     * @description To set the specific group header footer colors
//     * -----------------------------------------------------------------------------------------------------------------
//     * @param int $groupId
//     * @param array $groupHFSetting
//     * @return mixed
//     */
//    public function setGroupHeaderFooterSettings(int $groupId, array $groupHFSetting);
//
//    /**
//     * -----------------------------------------------------------------------------------------------------------------
//     * @description To set the specific group space related setting
//     * -----------------------------------------------------------------------------------------------------------------
//     * @param int $groupId
//     * @param array $groupSpaceSetting
//     * @return mixed
//     */
//    public function setGroupSpaceSetting(int $groupId, array $groupSpaceSetting);
//
//    /**
//     * -----------------------------------------------------------------------------------------------------------------
//     * @description To set the specific group texture setting
//     * -----------------------------------------------------------------------------------------------------------------
//     * @param int $groupId
//     * @param array $groupTextureSetting
//     * @return mixed
//     */
//    public function setGroupTextureSetting(int $groupId, array $groupTextureSetting);
//
//    /**
//     * -----------------------------------------------------------------------------------------------------------------
//     * @description To set the specific group colors
//     * -----------------------------------------------------------------------------------------------------------------
//     * @param int $groupId
//     * @param array $groupColors
//     * @return mixed
//     */
//    public function setGroupColors(int $groupId, array $groupColors);
//
//    /**
//     * -----------------------------------------------------------------------------------------------------------------
//     * @description To set the specific group tags color
//     * -----------------------------------------------------------------------------------------------------------------
//     * @param int $groupId
//     * @param array $groupTagColors
//     * @return mixed
//     */
//    public function setGroupTagColors(int $groupId, array $groupTagColors);
//
//    /**
//     * * -----------------------------------------------------------------------------------------------------------------
//     * @description To set the specific group registration page color
//     * -----------------------------------------------------------------------------------------------------------------
//     * @param int $groupId
//     * @param array $groupRegisterColor
//     * @return mixed
//     */
//    public function setGroupRegisterColors(int $groupId, array $groupRegisterColor);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update or set the multiple  group setting
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $groupId
     * @param array $settings
     * @return mixed
     */
    public function setGroupSettings(int $groupId, array $settings);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To fetch the technical settings, and the host data will be converted to users resource level
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupId
     * @return mixed
     */
    public function fetchTechnicalSettings($groupId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To fetch the super group or default group
     * -----------------------------------------------------------------------------------------------------------------
     * @param $accountId
     * @return mixed
     */
    public function getSuperGroup($accountId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the user current group Id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId
     * @return mixed
     */
    public function getUserCurrentGroup($userId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the user current group Ids
     * -----------------------------------------------------------------------------------------------------------------
     * @param $userId
     * @return mixed
     */
    public function getCurrentUserGroups($userId);


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find if user is group admin or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Group|null $group
     * @param int $userId
     * @return mixed
     */
    public function isUserGroupAdmin(?Group $group, int $userId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is responsible for copying all tags from one group and pasting the tags to another group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $from // group id from which tags need to be copy
     * @param $to // group id to which tags need to be pasted
     * @return mixed
     */
    public function copyGroupTags($from, $to);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To check if the current user have role of super pilot,super owner or super co-pilot
     *
     * @note Super word is used here which means user have above mentioned role in default group.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function isSuperPilotOrOwner();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will sync the labels for the specific group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Group $group
     * @return mixed
     */
    public function syncLabels(Group $group);

}
