<?php

namespace Modules\KctUser\Services\OtherModuleCommunication;

use Modules\KctAdmin\Entities\Event;
use Modules\KctAdmin\Entities\EventUser;
use Modules\KctAdmin\Entities\Setting;
use Modules\KctAdmin\Entities\Space;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will be managing the admin services
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IKctAdminService
 * @package Modules\KctUser\Services\OtherModuleCommunication
 */
interface IKctAdminService {

    /**
     * @param $key
     * @param $groupId
     * @return array|null
     */
    public function getSetting($key, $groupId): ?array;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Find event by event uuid
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @return mixed
     */
    public function findEvent($eventUuid);

    /**
     * @param $carbon
     * @return mixed
     */
    public function filterEventsByTime($carbon);

    /**
     * @param $eventUuid
     * @return mixed
     */
    public function getEventDataForQSS($eventUuid);

    /**
     * @param $eventUuid
     * @return mixed
     */
    public function getEventDataBeforeReg($eventUuid);

    /**
     * @param $spaceUuid
     * @return mixed
     */
    public function getEventWhereHasSpace($spaceUuid);

    /**
     * @param $eventUuid
     * @param $userId
     * @return mixed
     */
    public function findIsUserHostByHostId($eventUuid, $userId);

    /**
     * @param $workshopId
     * @return mixed
     */
    public function findEventByWorkshopId($workshopId);

    /**
     * @param $spaceCondition
     * @param $eventUserCondition
     * @param $dummy
     * @param $eventUuid
     * @return mixed
     */
    public function getEventWithSpaceAndConversations($spaceCondition, $eventUserCondition, $dummy, $eventUuid);

    /**
     * @param $eventId
     * @return mixed
     */
    public function getEventById($eventId);


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To find a space by space uuid
     * -----------------------------------------------------------------------------------------------------------------
     * @param $spaceUuid
     * @return mixed
     */
    public function findSpaceByUuid($spaceUuid): ?Space;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To attach the user with event as organiser
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @param $userId
     * @param null $spaceUuid
     * @param array $roles
     * @return EventUser|null
     */
    public function addUserToEvent($eventUuid, $userId, $spaceUuid = null, array $roles = []): ?EventUser;

    /**
     * @param $eventUuid
     * @return mixed
     */
    public function getEventDummyUsers($eventUuid);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the space with associated with event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $spaceUuid
     * @return mixed
     */
    public function getSpaceWithEvent($spaceUuid);

    /**
     * @param $conversation
     * @param $spaceUuid
     * @return mixed
     */
    public function loadSpaceWithRelationBySpaceUuid($conversation, $spaceUuid);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the default space for the event by event uuid
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @return mixed
     */
    public function getDefaultSpace($eventUuid);

    /**
     * @param $spaceUuid
     * @param $userId
     * @return mixed
     */
    public function getSpaceByUserIdAndSpaceUuid($spaceUuid, $userId);

    /// event user/////

    /**
     * @param $eventUuid
     * @param $userId
     * @return mixed
     */
    public function updateUserByEventUuidAndUserId($eventUuid, $userId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the all event tags
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function getAllEventTag();

    /**
     * @param $eventUuid
     * @return mixed
     */
    public function findVirtualEvent($eventUuid);

    /**
     * @param $dummyUserId
     * @param $eventUuid
     * @param $conversationUuid
     * @return mixed
     */
    public function getEventDummyUserForConv($dummyUserId, $eventUuid, $conversationUuid);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the dummy user data inside the conversation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @param $dummyUserId
     * @return mixed
     */
    public function getDummyUserDataInsideConv($eventUuid, $dummyUserId);

    /**
     * @param $conversationUuid
     * @return mixed
     */
    public function getDummyUserConversation($conversationUuid);

    /**
     * @param $conferenceId
     * @return mixed
     */
    public function getConferenceById($conferenceId);

    /**
     * @param $eventUuid
     * @param $type
     * @return mixed
     */
    public function findConferenceId($eventUuid, $type);

    /**
     * @param $eventUuid
     * @return mixed
     */
    public function getConferenceByEventUuid($eventUuid);

    /**
     * @param $eventUuid
     * @param $userId
     * @return mixed
     */
    public function getEvenUser($eventUuid, $userId);

    /**
     * @param $userId
     * @return mixed
     */
    public function getExistingUserTag($userId);

    /**
     * @param $userId
     * @return mixed
     */
    public function getNotExistingUserTag($userId);

    /**
     * @param $eventUsers
     * @param $op
     * @return mixed
     */
    public function prepareBuilderForEventList($eventUsers, $op);

    /**
     * @param $eventUuid
     * @param $userId
     * @return mixed
     */
    public function updateUserDnd($eventUuid, $userId);

    /**
     * @param $user
     * @return mixed
     */
    public function findUserActiveEventUuid($user);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the dummy user relation by space uuid and dummy user id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $space
     * @param $dmyUserId
     * @return mixed
     */
    public function validateDummyUserWithEvt($space, $dmyUserId);

    /**
     * @param $spaceUuid
     * @param $dmyUserId
     * @return mixed
     */
    public function findDummyUserForSpace($spaceUuid, $dmyUserId);

    /**
     * @param $eventUuid
     * @param $userId
     * @return mixed
     */
    public function findUserPassedJoinEvent($eventUuid, $userId);

    /**
     * @param $eventUuid
     * @return mixed
     */
    public function getAllEventSpace($eventUuid);

    /**
     * @param $event
     * @param $usersId
     * @param $column
     * @return mixed
     */
    public function findHostByUserId($event, $usersId, $column);

    /**
     * @param $eventUuid
     * @param $exclude
     * @return mixed
     */
    public function countDuoSpace($eventUuid, $exclude);

    /**
     * @param $userId
     * @param $eventUuid
     * @param $isP
     * @param $isM
     * @return mixed
     */
    public function updateOrCreateEventUser($userId, $eventUuid, $isP, $isM);

    /**
     * @param $eventUuid
     * @param $userId
     * @return mixed
     */
    public function getUserByEventUuidAndUserId($eventUuid, $userId);

    /**
     * @param $userId
     * @param $eventUuid
     * @return mixed
     */
    public function getHostById($userId, $eventUuid);

    /**
     * @param $eventUuid
     * @param $userId
     * @return mixed
     */
    public function countPassedJoinEventUser($eventUuid, $userId);

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
     * @return string|null
     */
    public function getEventImage(): ?string;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare embedded url for moment
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $moment
     * @return string|null
     */
    public function getMomentEmbeddedUrl($moment): ?string;

    /**
     * @param Event $event
     * @return array
     */
    public function getEventBroadcastingLinks(Event $event): array;

    /**
     * @param int $groupId
     * @return mixed
     */
    public function getLabels(int $groupId);

    /**
     * @return bool
     */
    public function isUserPilotOrOwner(): bool;

    /**
     * @return mixed
     */
    public function getUserCurrentGroupId();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the current user group id by auth id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function getCurrentUserGroupIds();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the group model by group key
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupKey
     * @return mixed
     */
    public function getGroupIdByGroupKey($groupKey);

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
     * @description To get the account default group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function getDefaultGroup();

    public function getAllGroup();

    /**
     * @param $groupIds
     * @return mixed
     */
    public function getGroupByIds($groupIds);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will return the group in which event is created.
     * -----------------------------------------------------------------------------------------------------------------
     * @param $eventUuid
     * @return mixed
     */
    public function findEventGroup($eventUuid);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the scenery data of specific event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @param $sendAssetUrl
     * @return mixed
     */
    public function fetchEventSceneryData($eventUuid, $sendAssetUrl);

}
