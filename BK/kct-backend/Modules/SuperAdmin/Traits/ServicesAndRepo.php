<?php


namespace Modules\SuperAdmin\Traits;


use Modules\SuperAdmin\Repositories\BaseRepo;
use Modules\SuperAdmin\Services\BaseService;

trait ServicesAndRepo {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To provide the base for other services
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return BaseService
     */
    public function suServices(): BaseService {
        return app(BaseService::class);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To provide the other repo access
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return BaseRepo
     */
    public function suRepo(): BaseRepo {
        return app(BaseRepo::class);
    }
}
