<?php


namespace Modules\UserManagement\Services\BusinessServices;


use App\Models\User;
use Exception;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will be managing the user services
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IUserService
 * @package Modules\UserManagement\Services\BusinessServices
 */
interface IUserService {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To create a user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param array $userData
     * @param int|null $groupId
     * @param int|null $groupRole
     * @return User|null
     * @throws Exception
     */
    public function createUser(array $userData, ?int $groupId = null, int $groupRole = null): ?User;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To update user entity relation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $userId
     * @param array $data
     * @return mixed
     */
    public function updateUserEntity(int $userId, array $data);

}
