<?php


namespace Modules\SuperAdmin\Repositories;


use Modules\SuperAdmin\Entities\User;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will provide the user Repository methods
 * ---------------------------------------------------------------------------------------------------------------------
 *
 */
interface IUserRepository {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the user by id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int|null $id
     * @return User|null
     */
    public function findById(?int $id): ?User;
}
