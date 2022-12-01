<?php


namespace Modules\KctUser\Traits;


use Modules\KctUser\Repositories\BaseRepo;

trait Repo {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To provide the other repo access
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return BaseRepo
     */
    public function userRepo(): BaseRepo {
        return app(BaseRepo::class);
    }

}
