<?php

namespace Modules\KctUser\Services\DataServices;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will be managing the user service related management
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IDataService
 * @package Modules\KctUser\Services\DataServices
 */
interface IDataService {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will be prepared the ban user details
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId
     * @param $severity
     * @param $banReason
     * @return mixed
     */
    public function prepareBanUserDetails($userId, $severity, $banReason);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To prepare the data for user invite
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @return mixed
     */
    public function prepareInviteUsers($request);

    /**
     * @param $user
     * @param int $object
     * @param $eventUuid
     * @return mixed
     */
    public function prepareUserForInvite($user, $object = 0, $eventUuid);

}
