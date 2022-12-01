<?php

namespace Modules\KctAdmin\Services\OtherModuleCommunication;

use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use phpDocumentor\Reflection\Types\Nullable;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This interface is used to manage all user related functionalities.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IUserManagementService
 *
 * @package Modules\KctAdmin\Services\OtherModuleCommunication
 */
interface IUserManagementService {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create a user within group and role
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param array $userData
     * @param int|null $groupId
     * @param int|null $groupRole
     * @return \Modules\SuperAdmin\Entities\User|null
     * @throws Exception
     */
    public function createUser(array $userData, int $groupId = null, int $groupRole = null): User;

    /**
     * @param $email
     * @return mixed
     */
    public function findByEmail($email): ?User;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the user data by user's Id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $id
     * @return mixed
     */
    public function findById(int $id): ?User;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the user language
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $id
     * @param string $lang
     * @return User|null
     */
    public function updateUserLang(int $id, string $lang): ?User;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update any data for the user personal fields like fname,lname,email,internal id etc.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $id
     * @param array $data
     * @return User|null
     */
    public function updateUserById(int $id, array $data): ?User;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To update the user entity data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param array $data
     * @param int $userId
     */
    public function updateUserEntity(array $data, int $userId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update user mobile number
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $id
     * @param array $data
     * @param bool $clearOther
     */
    public function updateUserMobile(int $id, array $data, bool $clearOther = false);

    /**
     * @param $userEmail
     * @param bool $includeDeleted
     * @return Collection
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To fetch multiple users data at a time by user's email.
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function getUserByEmail($userEmail, bool $includeDeleted = false): Collection;

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
    public function getUsersByEmail(array $emails, bool $allowTrashed=false): Collection;

    /**
     * ------------------------------––------------------------------––------------------------------––-----------------
     *
     * @description Get users collection by name or email
     * ------------------------------––------------------------------––------------------------------––-----------------
     *
     * @param string|null $key
     * @param array $filters
     * @return mixed
     */
    public function getUsersByNameOrEmail(?string $key, array $filters = []): Collection;

    /**
     * ------------------------------––------------------------------––------------------------------––-----------------
     * @description Get users collection by search
     * ------------------------------––------------------------------––------------------------------––-----------------
     *
     * @param string|null $key
     * @param array|null $search
     * @param array $filters
     * @return mixed
     */
    public function getUserForSearch(?string $key, ?array $search = [], array $filters = []);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get user not in event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string|null $key
     * @param string|null $uuId
     * @return mixed
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To fetch only those users which are not part of the given event
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function getUserNotInEvent(?string $key, ?string $uuId): collection;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is responsible for removing the multiple users from account at a time.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId
     * @return mixed
     */
    public function removeUsers($userId);

    /**
     * @param $usersId
     * @return mixed
     */
    public function getUsersById($usersId): Collection;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the user profile picture
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId
     * @param UploadedFile $file
     * @return mixed
     */
    public function uploadUserAvatar($userId, UploadedFile $file);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To search the entity
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $type
     * @param string $key
     * @return Collection
     */
    public function searchEntity(int $type, string $key): Collection;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To remove an entity from user profile
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $userId
     * @param int $entityId
     * @return mixed
     */
    public function deleteUserEntity(int $userId, int $entityId);

    public function findByName(?string $name): Collection;

    /**
     * @param string $email
     * @param array $data
     * @return mixed
     */
    public function updateUser(string $email, array $data);

    /**
     * @param array $data
     * @return mixed
     */
    public function fetchUserGroups($data);

    /**
     * @param array $userGroups
     * @return mixed
     */
    public function isUserPitotOfGroups($userGroups);

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
