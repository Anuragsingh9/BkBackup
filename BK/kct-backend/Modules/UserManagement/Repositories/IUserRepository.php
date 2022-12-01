<?php


namespace Modules\UserManagement\Repositories;


use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Modules\UserManagement\Entities\UserInfo;
use Modules\UserManagement\Entities\UserMobile;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will be managing the user repository management
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IUserRepository
 * @package Modules\UserManagement\Repositories
 */
interface IUserRepository {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the language for the user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string|null $lang
     * @param User|null $user
     * @return User|null;
     */
    public function updateUserLanguage(?string $lang, ?User $user = null): ?User;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the user data by id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $id
     * @param array $data
     * @return User
     */
    public function update(int $id, array $data): User;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create a user with provided arguments
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param array $userData
     * @param array $roles
     * @return User
     */
    public function createUser(array $userData, array $roles = []): User;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To add multiple mobiles for single user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $userId
     * @param array $userData
     * @param int $type
     * @return mixed
     */
    public function addMultipleMobiles(int $userId, array $userData, int $type);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @desription To check if user has mobiles number one of them is set to primary
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $userId
     * @param int $type
     * @return mixed
     */
    public function validateUserHasPrimaryNumber(int $userId, int $type);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create the user info
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $userId
     * @param array $userData
     * @return UserInfo
     */
    public function createUserInfo(int $userId, array $userData): UserInfo;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find an user by email.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $email
     * @param false $trashed
     * @return User|null
     */
    public function findByEmail($email, $trashed = false): ?User;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find the user by id.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int|null $id
     * @return User
     */
    public function findById(?int $id): ?User;

    /**
     * @param $email
     * @param bool $includeDeleted
     * @return mixed
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To fetch multiple users data at a time by user's email.
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function getUserByEmail($email, bool $includeDeleted = false);

    /**
     * ------------------------------––------------------------------––------------------------------––-----------------
     *
     * @description Get users collection by emails
     * ------------------------------––------------------------------––------------------------------––-----------------
     *
     * @param array $emails
     * @param bool $allowTrashed
     * @return mixed
     */
    public function getUsersByEmail(array $emails, bool $allowTrashed = false): Collection;

    /**
     * ------------------------------––------------------------------––------------------------------––-----------------
     *
     * @description Get users collection by emails
     * ------------------------------––------------------------------––------------------------------––-----------------
     *
     * @param string|null $key
     * @param array $filters
     * @return Collection
     */
    public function getByNameOrEmail(?string $key, array $filters = []): Collection;

    /**
     * ------------------------------––------------------------------––------------------------------––-----------------
     *
     * @description Get users collection by search
     * ------------------------------––------------------------------––------------------------------––-----------------
     *
     * @param string|null $key
     * @param array|null $search
     * @param array $filters
     * @return Collection
     */
    public function getForSearch(?string $key, ?array $search = [], array $filters = []);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To delete multiple users
     * -----------------------------------------------------------------------------------------------------------------
     * @param $userId
     * @return mixed
     */
    public function deleteMultipleUser($userId);

    /**
     * @param $email
     * @param $param
     * @return User
     */
    public function updateUserProfile($email, $param): User;

    /**
     * @param $data
     * @return mixed
     */
    public function fetchUserGroups($data);

    /**
     * @param $userGroups
     * @return mixed
     */
    public function isUserPitotOfGroups($userGroups);

    /**
     * @param $usersId
     * @return mixed
     */
    public function getUsersById($usersId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To delete the user mobile number record(s)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $userId
     * @return mixed
     */
    public function deleteMobile(int $userId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To add the mobile for user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $id
     * @param array $data
     * @return UserMobile
     */
    public function addMobile(int $id, array $data): ?UserMobile;

    public function findByName(?string $name, $like = false): Collection;

    /**
     * @param string|null $key
     * @param string|null $uuId
     * @return Collection
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To fetch only those users which are not part of the given event
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function getUserNotInEvent(?string $key, ?string $uuId): Collection;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get users  by Id and order them by user fields
     * -----------------------------------------------------------------------------------------------------------------
     * @param $userIds
     * @param string|null $orderBy
     * @param string|null $order
     * @return mixed
     */
    public function getUsersInOrder($userIds, ?string $orderBy, ?string $order);

}
