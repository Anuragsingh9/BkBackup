<?php


namespace Modules\SuperAdmin\Repositories;


use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website;
use Illuminate\Database\Eloquent\Collection;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will provide the Tenant Account Related Database Repository methods
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IAccountRepository
 * @package Modules\SuperAdmin\Repositories
 */
interface IAccountRepository {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create a empty Website
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return Website
     */
    public function createWebsite(): Website;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create the hostname
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $fqdn
     * @param Website $website
     * @return Hostname
     */
    public function createHostname(string $fqdn, Website $website): Hostname;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the all hostnames
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return Collection
     */
    public function getAllHostnames(): Collection;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the website by the hostname id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $id
     * @return Website|null
     */
    public function getWebsiteByHostnameId($id): ?Website;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the hostname by the name
     * @note it will auto append the APP hostname to account, if account name is `first.domain.com` pass `first` only
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string|null $hostname
     * @return Hostname|null
     */
    public function findHostnameByName(?string $hostname): ?Hostname;
}
