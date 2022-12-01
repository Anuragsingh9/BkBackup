<?php


namespace Modules\KctAdmin\Services\OtherModuleCommunication\factory;


use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Modules\KctAdmin\Services\OtherModuleCommunication\IUserManagementService;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\UserManagement\Repositories\IUserRepository;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class is used to manage all user related functionalities.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UserManagementService
 *
 * @package Modules\KctAdmin\Services\OtherModuleCommunication\factory
 */
class UserManagementService implements IUserManagementService {

    use ServicesAndRepo;
    use \Modules\UserManagement\Traits\ServicesAndRepo;
    use \Modules\UserManagement\Traits\ServicesAndRepo;


    /**
     * @inheritDoc
     */
    public function createUser(array $userData, int $groupId = null, int $groupRole = null): User {
        return $this->umServices()->userService->createUser($userData, $groupId, $groupRole);
    }

    /**
     * @param $email
     * @return User
     */
    public function findByEmail($email): ?User {
        return $this->umRepo()->userRepository->findByEmail($email);
    }

    /**
     * @inheritDoc
     */
    public function findById(int $id): ?User {
        return $this->umRepo()->userRepository->findById($id);
    }

    /**
     * @inheritDoc
     */
    public function updateUserLang(int $id, string $lang): ?User {
        $user = $this->umRepo()->userRepository->findById($id);
        return $this->umRepo()->userRepository->updateUserLanguage($lang, $user);
    }

    /**
     * @inheritDoc
     */
    public function updateUserById(int $id, array $data): ?User {
        return $this->umRepo()->userRepository->update($id, $data);
    }

    /**
     * @inheritDoc
     */
    public function updateUserEntity(array $data, int $userId) {
        return $this->umServices()->userService->updateUserEntity($userId, $data);
    }

    /**
     * @inheritDoc
     */
    public function updateUserMobile(int $id, array $data, bool $clearOther = false) {
        if ($clearOther) {
            $this->umRepo()->userRepository->deleteMobile($id);
        }
        if ($data['user_mobiles']['number'] ?? false) {
            $this->umRepo()->userRepository->addMobile($id, $data['user_mobiles']);
        }
        if ($data['user_phones']['number'] ?? false) {
            $this->umRepo()->userRepository->addMobile($id, $data['user_phones']);
        }
    }

    /**
     * @inheritDoc
     */
    public function getUserByEmail($userEmail, bool $includeDeleted = false): Collection {
        return $this->umRepo()->userRepository->getUserByEmail($userEmail, $includeDeleted);
    }

    /**
     * @inheritDoc
     */
    public function getUsersByEmail(array $emails, bool $allowTrashed = false): Collection {
        return $this->umRepo()->userRepository->getusersByEmail($emails, $allowTrashed);
    }

    public function getUsersByNameOrEmail(?string $key, array $filters = []): Collection {
        return $this->umRepo()->userRepository->getByNameOrEmail($key, $filters);
    }

    /**
     * @inheritDoc
     */
    public function getUserNotInEvent(?string $key, ?string $uuId): Collection{
        return $this->umRepo()->userRepository->getUserNotInEvent($key, $uuId);
    }

    /**
     * @inheritDoc
     */
    public function getUserForSearch(?string $key, ?array $search = [], array  $filters = []) {
        return $this->umRepo()->userRepository->getForSearch($key, $search, $filters);
    }

    /**
     * @inheritDoc
     */
    public function removeUsers($userId) {
        return $this->umRepo()->userRepository->deleteMultipleUser($userId);
    }

    /**
     * @param $usersId
     * @return mixed
     */
    public function getUsersById($usersId): Collection {
        return $this->umRepo()->userRepository->getUsersById($usersId);
    }

    /**
     * @inheritDoc
     */
    public function uploadUserAvatar($userId, UploadedFile $file) {
        $path = $this->adminServices()->fileService->storeFile($file, config('usermanagement.constants.s3.userAvatar'));
        $this->umRepo()->userRepository->update($userId, [
            'avatar' => $path,
        ]);
        return $path;
    }

    /**
     * @inheritDoc
     */
    public function searchEntity(int $type, string $key): Collection {
        return $this->umRepo()->entityRepository->findByName($key, $type, true);
    }

    /**
     * @inheritDoc
     */
    public function deleteUserEntity(int $userId, int $entityId) {
        return $this->umRepo()->entityRepository->deleteEntityUser($userId, $entityId);
    }

    public function findByName(?string $name): Collection {
        return $this->umRepo()->userRepository->findByName($name, true);
    }

    /**
     * @inheritDoc
     */
    public function updateUser(string $email, array $data) {
        return $this->umRepo()->userRepository->updateUserProfile($email, $data);
    }

    public function fetchUserGroups($data) {
        return $this->umRepo()->userRepository->fetchUserGroups($data);
    }

    public function isUserPitotOfGroups($userGroups) {
        return $this->umRepo()->userRepository->isUserPitotOfGroups($userGroups);
    }

    /**
     * @inheritDoc
     */
    public function getUsersInOrder($userIds, ?string $orderBy, ?string $order) {
        return $this->umRepo()->userRepository->getUsersInOrder($userIds, $orderBy, $order);
    }

}
