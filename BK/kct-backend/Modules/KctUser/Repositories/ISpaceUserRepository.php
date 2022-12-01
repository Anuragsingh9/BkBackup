<?php

namespace Modules\KctUser\Repositories;

interface ISpaceUserRepository {

    /**
     * @param $userId
     * @param $spaceUuid
     * @return mixed
     */
    public function findSpaceByUserIdAndSpaceUuid($userId, $spaceUuid);

    /**
     * @param $spaceUuid
     * @param $userId
     * @return mixed
     */
    public function countSpaceUser($spaceUuid, $userId);

    /**
     * @param $spaceUuid
     * @param $userIds
     * @param $conversationUuid
     * @return mixed
     */
    public function updateConversationUuid($spaceUuid, $userIds, $conversationUuid);

    /**
     * @param $userId
     * @param $spaceUuid
     * @param $role
     * @return mixed
     */
    public function createSpaceUser($userId, $spaceUuid, $role);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To remove an user from a given space
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId
     * @param $spaceUuid
     * @return mixed
     */
    public function deleteSpaceUser($userId, $spaceUuid);

}
