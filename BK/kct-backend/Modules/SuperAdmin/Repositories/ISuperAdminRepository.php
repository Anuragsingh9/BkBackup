<?php

namespace Modules\SuperAdmin\Repositories;

use Modules\SuperAdmin\Entities\Setting;
use Modules\SuperAdmin\Entities\SuperAdminUser;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will contain user Level Data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface ISuperAdminRepository
 * @package Modules\SuperAdmin\Repositories
 */
interface ISuperAdminRepository {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the users by email
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $email
     * @return mixed
     */
    public function getUserByEmail($email): ?SuperAdminUser;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the setting by key
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string|null $key
     * @return Setting|null
     */
    public function getSettingByKey(?string $key): ?Setting;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get all the Super Admin users
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function getAllSuperAdmins();

}
