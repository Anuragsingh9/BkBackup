<?php

namespace Modules\KctUser\Repositories;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will be managing the ban user repository management
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IBanUserRepository
 * @package Modules\KctUser\Repositories
 */
interface IBanUserRepository {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is used to get ban user by id and baned id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId
     * @param $banId
     * @return mixed
     */
    public function getBanUserByIdAndBanableId($userId, $banId);
}
