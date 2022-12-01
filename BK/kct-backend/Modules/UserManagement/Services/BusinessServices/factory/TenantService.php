<?php


namespace Modules\UserManagement\Services\BusinessServices\factory;


use Hyn\Tenancy\Environment;
use Modules\UserManagement\Services\BusinessServices\ITenantService;

class TenantService implements ITenantService {

    private Environment $tenant;

    public function __construct(Environment $tenant) {
        $this->tenant = $tenant;
    }

    /**
     * @inheritDoc
     */
    public function getFqdn(): ?string {
        $hostname = $this->tenant->hostname();
        return $hostname ? $hostname->fqdn : null;
    }
}
