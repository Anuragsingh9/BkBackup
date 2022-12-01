<?php

namespace Modules\KctUser\Services\BusinessServices;

use Modules\KctUser\Entities\Conversation;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will manage the space user services
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IKctUserSpaceService
 * @package Modules\KctUser\Services\BusinessServices
 */
interface IKctUserSpaceService {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Add user to space
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId
     * @param $spaceUuid
     * @param $eventUuid
     * @param $role
     * @return mixed
     */
    public function addUserToSpace($userId, $spaceUuid, $eventUuid, $role);

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton To get space
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param $spaceUuid
     * @return mixed
     */
    public function getSpace($spaceUuid);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the conversation of the user by user's id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId
     * @param $spaceUuid
     * @return mixed
     */
    public function getUserConversation($userId, $spaceUuid);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the count of how many users in conversation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $conversation
     * @return mixed
     */
    public function getConversationUserCount($conversation, $includeSpaceHost=true);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if host is present in conversation then marking the conversation as host was present in it
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Conversation $conversation
     * @return mixed
     */
    public function markConversationHost(Conversation $conversation);

}
