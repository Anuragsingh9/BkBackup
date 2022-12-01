<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Laravel\Passport\PersonalAccessClient;

class PassportPersonalAccessClient extends PersonalAccessClient {
    use UsesTenantConnection;
}
