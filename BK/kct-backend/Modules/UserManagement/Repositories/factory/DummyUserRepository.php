<?php


namespace Modules\UserManagement\Repositories\factory;


use Illuminate\Support\Facades\DB;
use Modules\UserManagement\Entities\DummyUser;
use Modules\UserManagement\Repositories\IDummyUserRepository;

class DummyUserRepository implements IDummyUserRepository {

    /**
     * @inheritDoc
     */
    public function createDummyUser(
        string $fname, string $lname, string $avatar, string $company, string $company_position, string $union,
        string $union_position, string $video_url
    ): DummyUser {
        return DummyUser::create([
            'fname'            => $fname,
            'lname'            => $lname,
            'avatar'           => $avatar,
            'company'          => $company,
            'company_position' => $company_position,
            'union'            => $union,
            'union_position'   => $union_position,
            'video_url'        => $video_url,
        ]);
    }
}
