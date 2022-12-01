<?php

namespace Modules\KctUser\Services\BusinessServices;

interface IKctUserEventService {
    /**
     * @param $request
     * @return mixed
     */
    public function addCurrentUserToEvent($request);

    /**
     * @param $event
     * @param $userId
     * @param $hostname
     * @return mixed
     */
    public function prepareEventEmailTags($event, $userId, $hostname);

    /**
     * @param $user
     * @return mixed
     */
    public function resolveUser($user);

    /**
     * @param $request
     * @return mixed
     */
    public function getDefaultHost($request);

    public function eventsForPilotsAndOwners($builder);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is use for to check is user added in the group or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     */
    public function isUserInGroup();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is use for to check is multi group enabled or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function isMultiGroupEnabled();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is use get the user groups
     * 1. if multi group enabled then
     *      a. if user is super pilot or owner the fetch all the groups
     *      b. else fetch all groups in which user added
     * 2, else fetch default group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @throws Exception
     * @return mixed
     */
    public function getUserGroups();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is use for to check is user member of group or not along with userId
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupId
     * @param $userId
     * @return mixed
     */
    public function isUserMemberOfGroup($groupId, $userId);

    /**
     * @param $request
     * @param $groupId
     * @param $op
     * @return mixed
     */
    public function getGroupEvents($request, $groupId, $op);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will check if user has participated in the event and has joined through registration and
     * accordingly return the Bool value.
     * -----------------------------------------------------------------------------------------------------------------
     * @param $eventUuid
     * @param $userId
     * @return bool
     */
    public function hasUserRegisteredEvent($eventUuid,$userId): bool;

    }
