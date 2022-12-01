<?php

namespace Modules\KctUser\Repositories;

use Modules\KctUser\Entities\ConversationUser;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will manage the conversation user management
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IConversationUserRepository
 * @package Modules\KctUser\Repositories
 */
interface IConversationUserRepository {

    /**
     * @param $conversationUuid
     * @param $userId
     * @param $userChime
     * @return mixed
     */
    public function createUserConv($conversationUuid, $userId, $userChime);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the conversation by user id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userIds
     * @param $conversationUuid
     * @return mixed
     */
    public function getConversationByUserId($userIds, $conversationUuid);

    /**
     * @param $conversationUuid
     * @return mixed
     */
    public function getConversationByConvUuid($conversationUuid);

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton This method use for conversation leave update
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param $conversationUuid
     * @param $conversationUsers
     * @return mixed
     */
    public function convLeaveUpdate($conversationUuid, $conversationUsers);

    /**
     * @param $conversationIds
     * @param $userId
     * @return mixed
     */
    public function updateLeaveConversation($conversationIds, $userId);

}
