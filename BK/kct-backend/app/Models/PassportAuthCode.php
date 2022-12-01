<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Laravel\Passport\AuthCode;

class PassportAuthCode extends AuthCode {
    use UsesTenantConnection;
}
