<?php


namespace Modules\SuperAdmin\Repositories\factory;


use Modules\SuperAdmin\Entities\User;
use Modules\SuperAdmin\Repositories\IUserRepository;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This repository is responsible for getting data from same application database directly
 * ---------------------------------------------------------------------------------------------------------------------
 */
class UserRepository implements IUserRepository {
    /**
     * @inheritDoc
     */
    public function findById(?int $id): ?User {
        return User::find($id);
    }
}
