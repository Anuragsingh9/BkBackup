<?php

namespace Modules\KctUser\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;

/**
 * @property int id
 * @property int user_id
 * @property string code
 * @property int otp_type
 * @property string created_at
 * @property string updated_at
 */
class OtpCode extends TenantModel {
    public static $type_Email = 1;
    public static $type_Password = 1;

    public static $OTP_validity_minutes = 15;

    protected $fillable = [
        'user_id',
        'code',
        'otp_type' // 1 Email, 2 Password Reset
    ];
}
