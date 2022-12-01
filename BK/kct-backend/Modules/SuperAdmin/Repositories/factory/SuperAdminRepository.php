<?php


namespace Modules\SuperAdmin\Repositories\factory;


use Modules\SuperAdmin\Entities\Setting;
use Modules\SuperAdmin\Entities\SuperAdminUser;
use Modules\SuperAdmin\Repositories\ISuperAdminRepository;

/**
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will contain the user level data
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class SuperAdminRepository
 * @package Modules\SuperAdmin\Repositories\factory
 */
class SuperAdminRepository implements ISuperAdminRepository {

    /**
     * @inheritDoc
     */
    public function getUserByEmail($email): ?SuperAdminUser {
        return SuperAdminUser::where('email', $email)->first();
    }

    /**
     * @inheritDoc
     */
    public function getSettingByKey(?string $key): ?Setting {
        return Setting::where('setting_key', $key)->first();
    }

    /**
     * @inheritDoc
     */
    public function getAllSuperAdmins() {
        return SuperAdminUser::all();
    }

}
