<?php

namespace Modules\KctAdmin\Repositories;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This interface will contain the group user management related functionality
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IGroupUserRepository
 * @package Modules\KctAdmin\Repositories
 */
interface IGroupUserRepository {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To add multiple user in group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupUser
     * @return mixed
     */
    public function storeMultipleGroupUser($groupUser);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To add group user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupId
     * @param $userId
     * @param null $groupRole
     * @return mixed
     */
    public function addGroupUser($groupId, $userId, $groupRole = null);

    /**
     * ------------------------------------------------------------------------------------------------------------------
     * @descripiton To get the group by role
     * ------------------------------------------------------------------------------------------------------------------
     *
     * @param $groupId
     * @param array $role
     * @return mixed
     */
    public function getGroupUsers($groupId, array $role);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To assign role of pilot to any user of the group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupId
     * @param $pilots
     * @return mixed
     */
    public function addUserAsPilot($groupId, $pilots);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Add or remove user's favourite group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupId
     * @param $isFavGroup
     * @return mixed
     */
    public function updateUserFavGroups($groupId, $isFavGroup);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if given group is user favourite group or not
     * -----------------------------------------------------------------------------------------------------------------
     * @param $groupId
     * @return mixed
     */
    public function isFavouriteGroup($groupId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To update the user last visited group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupId
     * @return mixed
     */
    public function updateUserLastVisitedGroup($groupId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the user current group id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId
     * @return mixed
     */
    public function getUserCurrentGroupId($userId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the user current groups
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId
     * @return mixed
     */
    public function getCurrentUserGroups($userId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the user group in which he was added for very first time
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId
     * @return mixed
     */
    public function getUserFirstAddedGroupId($userId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To remove users from given group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $id
     * @param $groupId
     * @return mixed
     */
    public function removeGroupUser($id, $groupId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Check the user role Like pilot, owner and user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function isUserPilotOrOwner(): bool;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the groups
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function getGroups();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the group user role
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupId
     * @param $userId
     * @return mixed
     */
    public function getUserGroupRole($groupId, $userId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To check the user is organiser(pilot, owner) or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId
     * @return mixed
     */
    public function isOrganiser($userId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is responsible for adding user as co-pilot of the group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupId
     * @param $coPilots
     * @return mixed
     */
    public function addUserAsCoPilot($groupId, $coPilots);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will update the group pilot
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupId
     * @param $pilot
     * @return mixed
     */
    public function updateGroupFirstPilot($groupId, $pilot);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is use for to check is user part of group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupId
     * @return mixed
     */
    public function isUserPartOfGroup($groupId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is use for to check is user super Pilot or Owner
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function isUserSuperPilotOrOwner();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is use for to find the user in groups or not with user id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupId
     * @param $userId
     * @return mixed
     */
    public function isUserMemberOfGroup($groupId, $userId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is use for to find the user is pilot of the group or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupId
     * @return mixed
     */
    public function isUserPilotOfGroup($groupId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is use for to find the user is co-pilot of the group or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupId
     * @return mixed
     */
    public function isUserCopilotOfGroup($groupId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is use for to find the user is owner of the group or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupId
     * @return mixed
     */
    public function isUserOwnerOfGroup($groupId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will update the group role of the user.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupId
     * @param $role
     * @param $userId
     * @return mixed
     */
    public function updateUserGroupRole($groupId, $role, $userId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is responsible for fetching all groups of an user in which user is added as a pilot
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId
     * @return mixed
     */
    public function getUserPilotGroups($userId);
    }
