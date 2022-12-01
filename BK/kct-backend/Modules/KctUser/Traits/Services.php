<?php


namespace Modules\KctUser\Traits;


use Modules\KctUser\Services\BaseService;

trait Services {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To provide the base for other services
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return BaseService
     */
    public function userServices(): BaseService {
        return app(BaseService::class);
    }
}
