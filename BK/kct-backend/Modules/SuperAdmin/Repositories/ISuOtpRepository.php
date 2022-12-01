<?php


namespace Modules\SuperAdmin\Repositories;

use Modules\SuperAdmin\Entities\OtpCode;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will contain OTP Related Database functionalities
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface ISuOtpRepository
 * @package Modules\SuperAdmin\Repositories
 */
interface  ISuOtpRepository {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create the OTP with email
     * @note if the otp code is not passed, a 6 digit random number will be choose as OTP
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $email
     * @param string|null $code
     * @param int $type
     * @return OtpCode
     */
    public function createOtp(string $email, string $code = null, int $type = 1): OtpCode;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the otp by email
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $email
     * @param int $type
     * @return OtpCode|null
     */
    public function getOtpByEmail(string $email, int $type = 1): ?OtpCode;
}
