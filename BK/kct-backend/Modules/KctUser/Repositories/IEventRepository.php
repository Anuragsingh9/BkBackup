<?php


namespace Modules\KctUser\Repositories;


use Modules\KctAdmin\Entities\Moment;
use Modules\KctUser\Entities\EventUser;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will handle the event management of events
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IEventRepository
 * @package Modules\KctUser\Repositories
 */
interface IEventRepository extends \Modules\KctAdmin\Repositories\IEventRepository {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To fetch an user from a specific event using user id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string|null $eventUuid
     * @param int|null $userId
     * @return EventUser|null
     */
    public function findParticipant(?string $eventUuid, ?int $userId): ?EventUser;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To prepare the event list query builder
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string|null $op
     * @return mixed
     */
    public function getEventListBuilder(?string $op, $groupIds = []);

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton Update the dummy user
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param $dummyId
     * @param $conversationUuid
     * @param $eventUuid
     * @return mixed
     */
    public function updateDummyUser($dummyId, $conversationUuid, $eventUuid);

    /**
     * ---------------------------------------------------------------------------------------------------------------------
     * @description This method is use for find the user active event
     * ---------------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function findUserActiveEventUuid();

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton Find the moment by moment id
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param string|null $momentId
     * @return Moment|null
     */
    public function findMomentByMomentId(?string $momentId): ?Moment;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the groups events
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function getGroupEvents();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create the event user log to store when the user joined/leave the event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @param $userId
     * @param null $leaveOn
     * @return mixed
     */
    public function createEventAttendLog($eventUuid, $userId, $leaveOn = null);
}
