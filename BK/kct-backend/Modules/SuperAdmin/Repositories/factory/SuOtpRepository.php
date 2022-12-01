<?php


namespace Modules\SuperAdmin\Repositories\factory;


use Modules\SuperAdmin\Entities\OtpCode;
use Modules\SuperAdmin\Repositories\ISuOtpRepository;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will provide OTP Repo Method Implementation
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class SuOtpRepository
 * @package Modules\SuperAdmin\Repositories\factory
 */
class SuOtpRepository implements ISuOtpRepository {

    /**
     * @inheritDoc
     */
    public function createOtp(string $email, string $code = null, int $type = 1): OtpCode {
        $code = $code ?: rand(100000, 999999);
        return OtpCode::updateOrCreate([
            'otp_type' => $type,
            'email'    => $email,
        ], [
            'code' => $code,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getOtpByEmail($email, int $type = 1): ?OtpCode {
        return OtpCode::where('email', $email)->where('otp_type', $type)->first();
    }
}
