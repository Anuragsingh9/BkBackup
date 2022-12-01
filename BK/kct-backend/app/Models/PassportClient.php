<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Laravel\Passport\Client;

class PassportClient extends Client {
    use UsesTenantConnection;
}
