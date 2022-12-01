<?php

namespace App\Generator;

use Hyn\Tenancy\Contracts\Website;
use Hyn\Tenancy\Generators\Uuid\ShaGenerator;

class TenantDBNameGenerator extends ShaGenerator {

    public function generate(Website $website): string {
        $implicit = parent::generate($website);
        return env("DB_TENANT_PREFIX") . $implicit;
    }
}
