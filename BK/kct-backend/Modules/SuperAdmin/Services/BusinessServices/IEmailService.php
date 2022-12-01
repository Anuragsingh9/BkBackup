<?php


namespace Modules\SuperAdmin\Services\BusinessServices;


use Modules\SuperAdmin\Entities\Organisation;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will contain Account Level Organisation Data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IEmailService
 * @package Modules\SuperAdmin\Services\BusinessServices
 */
interface IEmailService {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To send OTP to email
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $otp
     * @param string $email
     * @return mixed
     */
    public function sendSuOtp(string $otp, string $email);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To send the account name reset email
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Organisation $organisation
     * @return mixed
     */
    public function sendAccountReset(Organisation $organisation);
}
