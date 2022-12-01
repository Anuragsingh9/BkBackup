<?php


namespace Modules\KctAdmin\Traits;


use Modules\KctAdmin\Repositories\BaseRepo;
use Modules\KctAdmin\Services\BaseService;

trait ServicesAndRepo {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To provide the base for other services
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return BaseService
     */
    public function adminServices(): BaseService {
        return app(BaseService::class);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To provide the other repo access
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return BaseRepo
     */
    public function adminRepo(): BaseRepo {
        return app(BaseRepo::class);
    }
}
