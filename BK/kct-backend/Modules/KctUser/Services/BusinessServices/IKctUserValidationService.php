<?php

namespace Modules\KctUser\Services\BusinessServices;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will be managing the user validation services
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IKctUserValidationService
 * @package Modules\KctUser\Services\BusinessServices
 */
interface IKctUserValidationService {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This will check the event either started or yet to start or space is still opened
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return bool
     */
    public function isEventSpaceOpenOrFuture($event): bool;

    /**
     * @param $eventUuid
     * @param $userId
     * @return mixed
     */
    public function isUsersAlreadySpaceHost($eventUuid, $userId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To check the event is present or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return mixed
     */
    public function resolveEvent($event);

    /**
     * @param $user
     * @return mixed
     */
    public function resolveUser($user);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To check if the space have available seat
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @param $spaceUuid
     * @param false $allowDefault
     * @param false $allowException
     * @return mixed
     */
    public function isSpaceHaveSeat($eventUuid, $spaceUuid, bool $allowDefault = false, bool $allowException = false);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if user can join the conversation.
     * @note User cannot join in private conversation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId
     * @param $spaceUuid
     * @param $isDummy
     * @returns bool
     */
    public function canUserJoinConversation($userId, $spaceUuid, $isDummy): bool;

    /**
     * @param $eventUuid
     * @return mixed
     */
    public function getEventCreateByUserId($eventUuid);

}
