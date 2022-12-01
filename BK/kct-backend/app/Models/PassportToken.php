<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Laravel\Passport\Token;

class PassportToken extends Token {
    use UsesTenantConnection;
}
