<?php


namespace Modules\SuperAdmin\Services\OtherModuleCommunication;

use Exception;
use Modules\SuperAdmin\Entities\User;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will contain the user management methods
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IUserManagement
 * @package Modules\SuperAdmin\Services\OtherModuleCommunication\Repositories
 */
interface IUserManagement {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create a user within group and role
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param array $userData
     * @param int|null $groupId
     * @param int|null $groupRole
     * @return User|null
     * @throws Exception
     */
    public function createUser(array $userData, int $groupId = null, int $groupRole = null): User;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the user possible roles list
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function getRoles(): array;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the user by email address
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string|null $email
     * @param false $trashed
     * @return \App\Models\User|null
     */
    public function findUserByEmail(?string $email, $trashed=false): ?\App\Models\User;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the url
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $type
     * @param array $data
     * @return string|null
     */
    public function prepareUrl(string $type, array $data): ?string;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To attach user with company
     * -----------------------------------------------------------------------------------------------------------------
     * @param $userId
     * @param $data
     * @return mixed
     */
    public function updateUserEntity($userId, $data);
}
