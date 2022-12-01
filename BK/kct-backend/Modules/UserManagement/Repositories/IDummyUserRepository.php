<?php


namespace Modules\UserManagement\Repositories;


use App\Models\User;
use Modules\UserManagement\Entities\DummyUser;

interface IDummyUserRepository {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to create a dummy user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $fname
     * @param string $lname
     * @param string $avatar
     * @param string $company
     * @param string $company_position
     * @param string $union
     * @param string $union_position
     * @param string $video_url
     * @return DummyUser
     */
    public function createDummyUser(
        string $fname, string $lname, string $avatar, string $company, string $company_position, string $union,
        string $union_position, string $video_url
    ): DummyUser;
}
