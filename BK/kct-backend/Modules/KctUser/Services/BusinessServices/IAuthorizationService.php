<?php

namespace Modules\KctUser\Services\BusinessServices;

interface IAuthorizationService {
    /**
     * @param $eventUuid
     * @return mixed
     */
    public function isUserEventMember($eventUuid);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To validate if user can change the space
     * @info This will check if first user is host of current space
     * - As from the requirement host can change spaces where user is host in those spaces
     * - Then check for the vip space validation if user is vip then can only enter in vip
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $targetSpace
     * @return mixed
     */
    public function validateNewUserForSpaceChange($targetSpace);

    /**
     * @param $userId
     * @param null $eventUuid
     * @param null $spaceUuid
     * @return mixed
     */
    public function isUserStateAvailable($userId, $eventUuid = null, $spaceUuid = null);

}
