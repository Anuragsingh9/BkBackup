<?php


namespace Modules\UserManagement\Traits;


use Modules\UserManagement\Repositories\BaseRepository;
use Modules\UserManagement\Services\BaseService;

trait ServicesAndRepo {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To provide the base for other services
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return BaseService
     */
    public function umServices(): BaseService {
        return app(BaseService::class);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To provide the other repo access
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return BaseRepository
     */
    public function umRepo(): BaseRepository {
        return app(BaseRepository::class);
    }
}
