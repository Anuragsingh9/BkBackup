<?php

namespace Modules\KctUser\Services\OtherModuleCommunication;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will be managing the user management services
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IUserManagementService
 *
 * @package Modules\KctUser\Services\OtherModuleCommunication
 */
interface IUserManagementService {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method used to create the otp
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param User $user
     * @return string
     */
    public function createOtp(User $user): string;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the user by id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $id
     * @return mixed
     */
    public function findById(int $id): ?User;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the user by email
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string|null $email
     * @return mixed
     */
    public function findByEmail(?string $email): ?User;


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To search the entity
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $type
     * @param string $key
     * @param bool $filterAlreadyAttached
     * @return Collection
     */
    public function searchEntity(int $type, string $key, bool $filterAlreadyAttached = false): Collection;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method can perform 3 task conditionally
     * 1. Update Existing Entity Relation with user
     * 2. Create new entity relation with user
     * 3. Create new entity and add that user to entity
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId
     * @param $data
     */
    public function updateUserEntity($userId, $data);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update user column visibility data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId
     * @param $data
     * @return mixed
     */
    public function updateUserVisibility($userId, $data);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To upload the user avatar
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param UploadedFile|string $file
     * @return string|null
     */
    public function uploadUserAvatar($file): ?string;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To add new user on the account from HE side
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $data
     * @param int|null $groupId
     * @return mixed
     */
    public function createUser($data, int $groupId = null);


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To change password of login user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $id
     * @param $data
     * @return mixed
     */
    public function updateUserById($id, $data);
}
