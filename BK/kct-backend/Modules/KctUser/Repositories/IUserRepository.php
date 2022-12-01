<?php

namespace Modules\KctUser\Repositories;

use App\Models\User;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This interface will contain user related Management
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IUserRepository
 * @package Modules\KctUser\Repositories
 */
interface IUserRepository {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get user by id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $id
     * @return User|null
     */
    public function getUserById(string $id): ?User;

    public function getUserEvents($id);
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To create user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param array $data
     * @return User|null
     */
    public function createUser(array $data): ?User;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To create otp
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param User|null $user
     * @param null $type
     * @return mixed
     */
    public function createOtp(?User $user, $type = null);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To fetch the send OTP of an user by user id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId
     * @param null $type
     * @return mixed
     */
    public function getOtp($userId, $type = null);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get user event invites
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @return mixed
     */
    public function getUserEventInvites($eventUuid);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get count of send invited email to the user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $invites
     * @return mixed
     */
    public function getInvitedEmailCount($invites);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To insert invited user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $data
     * @return mixed
     */
    public function insertInvite($data);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To remove the entity
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId
     * @param $entityId
     * @return mixed
     */
    public function removeEntity($userId, $entityId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method use for create the log
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @return mixed
     */
    public function storeLogs($request);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will find user by email.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $email
     * @return mixed
     */
    public function getUserByEmail($email);

}
