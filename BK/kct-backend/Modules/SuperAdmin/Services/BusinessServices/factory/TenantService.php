<?php


namespace Modules\SuperAdmin\Services\BusinessServices\factory;


use App\Models\User;
use Hyn\Tenancy\Contracts\Hostname;
use Hyn\Tenancy\Contracts\Website;
use Hyn\Tenancy\Environment;
use Modules\SuperAdmin\Services\BusinessServices\ITenantService;

/**
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will contain the tenant related functionality
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class TenantService
 * @package Modules\SuperAdmin\Services\BusinessServices\factory
 */
class TenantService implements ITenantService {

    private Environment $tenant;

    public function __construct(Environment $tenant) {
        $this->tenant = $tenant;
    }

    /**
     * @inheritDoc
     */
    public function setTenantByWebsite(Website $website): ?Website {
        return $this->tenant->tenant($website);
    }

    /**
     * @inheritDoc
     */
    public function setTenantByHostname(Hostname $hostname): ?Hostname {
        return $this->tenant->hostname($hostname);
    }

    /**
     * @inheritDoc
     */
    public function getHostname(): ?Hostname {
        return $this->tenant->hostname();
    }

    /**
     * @inheritDoc
     */
    public function getWebsite(): ?Website {
        return $this->tenant->website();
    }

    /**
     * @inheritDoc
     */
    public function setWebsiteByHostId(string $id): ?Hostname {
        $hostname = \Hyn\Tenancy\Models\Hostname::with('website')->find($id);
        if ($hostname) {
            $this->tenant->tenant($hostname->website);
            return $this->tenant->hostname();
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getHost($request): string {
        $hostname = $request->getHost();
        return $hostname;
    }

    /**
     * @inheritDoc
     */
    public function setTenantByHostnameId(string $id): ?\Hyn\Tenancy\Models\Hostname {
        $hostname = \Hyn\Tenancy\Models\Hostname::find($id);
        $this->tenant->tenant($hostname->website);
        return $hostname;
    }
}
