<?php

namespace Modules\KctAdmin\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\KctAdmin\Entities\Event;
use Modules\KctAdmin\Entities\EventDummyUser;
use Modules\KctAdmin\Entities\EventUser;
use Modules\KctAdmin\Entities\GroupEvent;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will manage the event related functionality
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IEventRepository
 * @package Modules\KctAdmin\Repositories
 */
interface IEventRepository {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will create event and attach the event with group in which the event is created.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $param
     * @return Event
     */
    public function create($param): Event;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To attach the event with group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @param $groupId
     * @return GroupEvent|null
     */
    public function attachEventGroup($eventUuid, $groupId): ?GroupEvent;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To attach the user with event according to role provided
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
     * -----------------------------------------------------------------------------------------------------------------
     * @description To attach the user in event and mark as registered
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $user
     * @return mixed
     */
    public function addUserAndMarkRegistered($request, $user);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find an event by event uuid
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event_uuid
     * @return Event|null
     */
    public function findByEventUuid($event_uuid): ?Event;


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Event $event
     * @param array $data
     * @return mixed
     */
    public function updateEvent(Event $event, array $data);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the events list with filter applied
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param null $tense
     * @param int $limit
     * @param null $eventUuid
     * @param false $isPaginated
     * @param int $groupId
     * @return mixed
     */
    public function getEvents($tense = null, $limit = 10, $eventUuid = null, $isPaginated = false, $groupId = 1);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get all the events
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param false $trashed
     * @return mixed
     */
    public function getAllEvents($trashed = false);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the draft events list with filter applied
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $limit
     * @param false $isPaginated
     * @param int $groupId
     * @return mixed
     */
    public function getGroupDraftEvents(int $limit = 10, bool $isPaginated = false, int $groupId = 1);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the event spaces
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $eventUuid
     * @return Collection
     */
    public function getEventSpaces(string $eventUuid): Collection;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the space hosts
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $space
     * @param $hosts
     * @return mixed
     */
    public function updateSpaceHosts($space, $hosts);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To remove the user from the spaces where user is not host in spaces
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Event $event
     * @param int $userId
     * @return mixed
     */
    public function removeAsUserFromSpace(Event $event, int $userId);


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the all users of event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string|null $eventUuid
     * @param null $role
     * @return Collection
     */
    public function getEventUsers(?string $eventUuid, $role = null): Collection;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get event participant users(VIP, Participant users)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $eventUuid
     * @param $rowPerPage
     * @param $isPaginated
     * @param $key
     * @param $orderBy
     * @param $order
     * @return mixed
     */
    public function getParticipantUsers(string $eventUuid,$rowPerPage, $isPaginated,$key,$orderBy,$order);

    /**
     * @param $eventUuid
     * @param $rowPerPage
     * @param $isPaginated
     * @param $key
     * @param $orderBy
     * @param $order
     * @return mixed
     */
    public function getEventUserInOrderBy($eventUuid, $rowPerPage, $isPaginated,$key,$orderBy,$order);

    /**
     * @param $eventUuid
     * @param $rowPerPage
     * @param $isPaginated
     * @param $key
     * @param $orderBy
     * @param $order
     * @return mixed
     */
    public function getEventUserInOrderByComp($eventUuid, $rowPerPage, $isPaginated, $key,$orderBy,$order);

        /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get event team users(speaker, moderator, organisers, expert, team)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $eventUuid
     * @param $rowPerPage
     * @param $isPaginated
     * @param $key
     * @return mixed
     */
    public function getEventTeamUsers(string $eventUuid, $rowPerPage, $isPaginated, $key);

    /**
     * @param $start_time
     * @param $title
     * @return Event|null
     */
    public function isDuplicateEvent($start_time, $title): ?Event;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the all dummy users
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return Collection
     */
    public function getDummyUsers(): Collection;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the all dummy users
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $dummyId
     * @param $eventUuid
     * @param $spaceUuid
     * @return EventDummyUser|null
     */
    public function addDummyUser($dummyId, $eventUuid, $spaceUuid): ?EventDummyUser;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the dummy user relation by space uuid and dummy user id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $spaceUuid
     * @param $dummyId
     */
    public function findDummyRelationBySpaceUuid($spaceUuid, $dummyId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the dummy user relation by event and dummy user id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @param $dummyId
     */
    public function findDummyRelation($eventUuid, $dummyId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To make event as draft event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $param
     * @return mixed
     */
    public function makeEventAsDraft($param);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To fetch all drafts events
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function getDraftEvents();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find a draft event
     * -----------------------------------------------------------------------------------------------------------------
     * @param $event_uuid
     * @return mixed
     */
    public function findDraftEvent($event_uuid);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update or create new draft event details
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @param $dataToUpdate
     * @return mixed
     */
    public function updateOrCreateDraft($eventUuid, $dataToUpdate);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will store the value of auto key moments switch for event.
     * @note:-
     *  if value is 0 -> key moments needs to be created manually.
     *  if value is 1 -> key moments for entire event will be made automatically.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $value
     * @param $eventUuid
     * @return mixed
     */
    public function storeEventAutoKeyMoment($value, $eventUuid): bool;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the event team members id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return mixed
     */
    public function getEventTeamMembersId($event);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the event expert member ids
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return mixed
     */
    public function getEventExpertMembersId($event);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the event speaker member ids
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return mixed
     */
    public function getEventSpeakerId($event);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the event moderator ids
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return mixed
     */
    public function getEventModeratorId($event);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the event participant ids
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return mixed
     */
    public function getEventParticipantsId($event);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the event vip member ids
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return mixed
     */
    public function getEventVIPMembersId($event);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is responsible for making an event as recurring event.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $param
     * @return mixed
     */
    public function makeEventRecurring($param);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the event by using the join code and the second parameter will exclude the event from
     * fetching
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $joinCode
     * @param array $excludingEventsId
     * @param bool $allowPast To allow the past events
     * @return mixed
     */
    public function getEventByJoinCode($joinCode, array $excludingEventsId = [], $allowPast = false);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is responsible for updating the event recurrence related data.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $param
     * @param $eventUuid
     * @return mixed
     */
    public function updateEventRecurringData($param, $eventUuid);

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
     * @descripiton To get the events from recurrence
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupIds
     * @param $startDate
     * @param $endDate
     * @param string|null $key
     * @param bool $allowAllDay
     * @return mixed
     */
    public function getEventFromRecurrence($groupIds, $startDate, $endDate, ?string $key, $allowAllDay=false);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the event by the type of event
     * e.g. Cafeteria
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $type
     * @return mixed
     */
    public function findByType(int $type);
}

