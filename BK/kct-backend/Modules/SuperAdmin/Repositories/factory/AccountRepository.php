<?php


namespace Modules\SuperAdmin\Repositories\factory;


use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website;
use Illuminate\Database\Eloquent\Collection;
use Modules\SuperAdmin\Entities\Organisation;
use Modules\SuperAdmin\Repositories\IAccountRepository;

/**
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will contain the account related methods
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class AccountRepository
 * @package Modules\SuperAdmin\Repositories\factory
 */
class AccountRepository implements IAccountRepository {

    /**
     * @inheritDoc
     */
    public function createWebsite(): Website {
        $website = new Website;
        app(WebsiteRepository::class)->create($website);
        return $website;
    }

    /**
     * @inheritDoc
     */
    public function createHostname(string $fqdn, Website $website): Hostname {
        $hostname = new Hostname();
        $hostname->fqdn = $fqdn;
        $hostname = app(HostnameRepository::class)->create($hostname);
        app(HostnameRepository::class)->attach($hostname, $website);
        return $hostname;
    }

    /**
     * @inheritDoc
     */
    public function getAllHostnames(): Collection {
        $organisations = Organisation::whereHas('hostname')->get();
        $hostnames = new Collection();
        foreach($organisations as $organisation) {
            $hostname =  $organisation->hostname;
            $hostname->organisation = $organisation;
            $hostnames->push($hostname);
        }
        return $hostnames;
    }

    /**
     * @inheritDoc
     */
    public function getWebsiteByHostnameId($id): ?Website {
        $hostname = Hostname::find($id);
        if ($hostname) {
            return $hostname->website;
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function findHostnameByName(?string $hostname): ?Hostname {
        $host = "$hostname." . env("APP_HOST");
        return Hostname::where('fqdn', $host)->first();
    }
}
