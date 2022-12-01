<?php


namespace Modules\SuperAdmin\Services\BusinessServices;


use Hyn\Tenancy\Contracts\Hostname;
use Hyn\Tenancy\Contracts\Website;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will contain the tenant services
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface ITenantService
 * @package Modules\SuperAdmin\Services\BusinessServices
 */
interface ITenantService {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To set the tenant by website object
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Website $website
     * @return Website|null
     */
    public function setTenantByWebsite(Website $website): ?Website;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To set the tenant by hostname object
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Hostname $hostname
     * @return Hostname|null
     */
    public function setTenantByHostname(Hostname $hostname): ?Hostname;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to get the current hostname
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return Hostname|null
     */
    public function getHostname(): ?Hostname;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the current tenant website
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return Website|null
     */
    public function getWebsite(): ?Website;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To set the website by hostname id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $id
     * @return Hostname|null
     */
    public function setWebsiteByHostId(string $id): ?Hostname;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the host
     * -----------------------------------------------------------------------------------------------------------------
     * @param $request
     * @return string
     */
    public function getHost($request): string;

    /**
     * ----------------–----------------–----------------–----------------–----------------–----------------–-----------
     *
     * @description To set the hostname by hostname id
     * ----------------–----------------–----------------–----------------–----------------–----------------–-----------
     *
     * @param string $id
     * @return mixed
     */
    public function setTenantByHostnameId(string $id): ?\Hyn\Tenancy\Models\Hostname;

}
