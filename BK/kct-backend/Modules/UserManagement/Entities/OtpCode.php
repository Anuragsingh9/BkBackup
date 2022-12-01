<?php

namespace Modules\UserManagement\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;

class OtpCode extends TenantModel {

    protected $fillable = [
        'user_id',
        'code',
        'otp_type', // 1 Email Otp, 2. Password Reset OTP,
    ];

}
