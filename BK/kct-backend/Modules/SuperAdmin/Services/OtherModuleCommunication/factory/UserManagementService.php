<?php


namespace Modules\SuperAdmin\Services\OtherModuleCommunication\factory;

use Modules\SuperAdmin\Entities\User;
use Modules\SuperAdmin\Services\OtherModuleCommunication\IUserManagement;
use Modules\SuperAdmin\Traits\ServicesAndRepo;

/**
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will contain the user management services
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class UpdateAccountSettings
 * @package Modules\SuperAdmin\Repositories\factory
 */
class UserManagementService implements IUserManagement {
    use ServicesAndRepo;
    use \Modules\UserManagement\Traits\ServicesAndRepo;

    /**
     * @inheritDoc
     */
    public function createUser(array $userData, int $groupId = null, int $groupRole = null): User {
        $user = $this->umServices()->userService->createUser($userData, $groupId, $groupRole);
        return $this->suRepo()->userRepository->findById($user ? $user->id : null);
    }

    /**
     * @inheritDoc
     */
    public function getRoles(): array {
        return config('usermanagement.auth.roles');
    }

    /**
     * @inheritDoc
     */
    public function findUserByEmail(?string $email, $trashed=false): ?\App\Models\User {
        return $this->umRepo()->userRepository->findByEmail($email, $trashed);
    }

    /**
     * @inheritDoc
     */
    public function prepareUrl(string $type, array $data): ?string {
        return $this->umServices()->kctService->prepareUrl($type, $data);
    }

    /**
     * @inheritDoc
     */
    public function updateUserEntity($userId,$data){
        return $this->umServices()->userService->updateUserEntity($userId,$data);
    }
}
