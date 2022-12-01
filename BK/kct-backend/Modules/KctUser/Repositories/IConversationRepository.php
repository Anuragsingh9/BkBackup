<?php

namespace Modules\KctUser\Repositories;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will manage the event related functionality
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IConversationRepository
 * @package Modules\KctAdmin\Repositories
 */
interface IConversationRepository {

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
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton This method use for get the conversation
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param $uuid
     * @return mixed
     */
    public function getConversation($uuid);

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton To get user current conversation
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @param $eventUser
     * @return mixed
     */
    public function getUserCurrentConversation($eventUuid, $eventUser);

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the conversation user for remove the user from conversation
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param $conversationUuid
     * @return mixed
     */
    public function getConversationUserToRemove($conversationUuid);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To create the conversation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $spaceUuid
     * @return mixed
     */
    public function createConversation($spaceUuid);

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton To get all space conversation
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param $spaceUuid
     * @return mixed
     */
    public function getSpaceAllConversation($spaceUuid);

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton To get all virtual background images
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function getSystemVirtualBGImages();


}
