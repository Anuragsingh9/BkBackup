<?php

namespace Modules\KctUser\Services\BusinessServices;

use App\Models\User;
use Exception;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will be managing the user services
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IKctUserService
 * @package Modules\KctUser\Services\BusinessServices
 */
interface IKctUserService {

    /**
     * @return mixed
     */
    public function prepareHashCode();

    /**
     * @param $request
     * @param $bool
     * @param $data
     * @return mixed
     */
    public function register($request, $bool, $data);

    /**
     * @param $otp
     * @param null $user
     * @return mixed
     */
    public function otpVerify($otp, $user = NULL);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the user full badge
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId
     * @param null $eventUuid
     * @return User|null
     * @throws Exception
     */
    public function getUserBadge($userId, $eventUuid = null): ?User;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will prepare image to upload  by cropping conditionally
     * -----------------------------------------------------------------------------------------------------------------
     * @param $image
     * @return mixed
     */
    public function prepareAvatar($image);


}
