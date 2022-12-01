<?php


namespace Modules\UserManagement\Services\BusinessServices;


interface ITenantService {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the current host name fqdn as well
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return string|null
     */
    public function getFqdn(): ?string;
}
