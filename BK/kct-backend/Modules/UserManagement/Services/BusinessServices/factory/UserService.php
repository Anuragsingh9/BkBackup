<?php


namespace Modules\UserManagement\Services\BusinessServices\factory;


use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\UserManagement\Entities\GroupUser;
use Modules\UserManagement\Services\BusinessServices\IUserService;
use Modules\UserManagement\Traits\ServicesAndRepo;

class UserService implements IUserService {
    use ServicesAndRepo;
    use \Modules\KctAdmin\Traits\ServicesAndRepo;

    /**
     * @inheritDoc
     */
    public function createUser(array $userData, ?int $groupId = null, int $groupRole = null): ?User {
        // creating user object
        $user = $this->umRepo()->userRepository->createUser($userData, $userData['roles'] ?? []);
        // taking group id if not provided adding user to default group
        $groupId = $groupId ?: $this->adminRepo()->groupRepository->getDefaultGroup()->id;
        // if group role not specified making user as regular user only
        $groupRole = $groupRole ?: GroupUser::$role_User;
        // adding user in group
        $this->adminRepo()->groupRepository->addUser($user->id, $groupId, $groupRole);
        // if group role is pilot(2) or owner(3) then sending the welcome email to user
        if (in_array($groupRole, [2, 3])) {
            $this->umServices()->emailService->sendWelcomeEmail($user, $groupId, $groupRole);
        }
        return $user;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function updateUserEntity(int $userId, array $data) {
        if (isset($data['id'])) {
            $entity = $this->umRepo()->entityRepository->findById($data['id']);
        } else if (isset($data['long_name'])) {
            $entity = $this->umRepo()->entityRepository->findByName($data['long_name'])->first();
            $entity = $entity ?: $this->umRepo()->entityRepository->create([
                'long_name'      => $data['long_name'],
                'entity_type_id' => $data['entity_type_id'],
            ]);
        } else {
            throw new Exception('Invalid Entity Selector');
        }

        $replaceId = isset($data['old_entity_id'])
        && $data['old_entity_id']
        && is_numeric($data['old_entity_id'])
            ? $data['old_entity_id']
            : null;

        $this->umRepo()->entityRepository->attachUserToEntity(
            $userId,
            $entity->id,
            $replaceId,
            $data['position'] ?? null
        );
    }

}
