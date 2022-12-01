<?php

namespace Modules\KctUser\Services\BusinessServices;

use Exception;
use App\Models\User;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This interface  will be managing the email related management
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IEmailService
 * @package Modules\KctUser\Services\BusinessServices
 */
interface IEmailService {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To send OTP code and encrypted url of the OTP page to the user's email
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param User $user
     * @param $eventUuid
     * @return string
     * @throws Exception
     */
    public function sendOtp(User $user, $eventUuid): string;

    /**
     * @param $event
     * @param $user
     * @return mixed
     */
    public function sendModeratorInfo($event, $user);

    /**
     * @param $event
     * @param $userId
     * @param $data
     * @return mixed
     */
    public function sendVirtualRegistration($event, $userId, $data);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to send reset password link into the user email
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $email
     * @param $rootLink
     * @return mixed
     * @throws Exception
     */
    public function sendForgetPassword($email, $rootLink);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To send successful registration email into the event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return mixed
     */
    public function sendEventRegSuccess($event);
}
