<?php


namespace Modules\KctUser\Repositories\factory;


use Modules\KctUser\Entities\UserBan;
use Modules\KctUser\Repositories\IBanUserRepository;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will be managing the ban user repository management
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class BanUserRepository
 * @package Modules\KctUser\Repositories\factory
 */
class BanUserRepository implements IBanUserRepository {

    /**
     * @inheritDoc
     */
    public function getBanUserByIdAndBanableId($userId, $banId) {
        return UserBan::where('user_id', $userId)->where('ban_type', 'event')->where('banable_id', $banId)->first();
    }

}
