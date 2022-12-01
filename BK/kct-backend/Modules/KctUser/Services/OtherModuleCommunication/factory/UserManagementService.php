<?php


namespace Modules\KctUser\Services\OtherModuleCommunication\factory;


use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Intervention\Image\Facades\Image;
use Modules\KctUser\Services\OtherModuleCommunication\IUserManagementService;
use Modules\UserManagement\Entities\OtpCode;
use Modules\UserManagement\Traits\ServicesAndRepo;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will be managing the user management services
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UserManagementService
 * @package Modules\KctUser\Services\OtherModuleCommunication\factory
 */
class UserManagementService implements IUserManagementService {

    use ServicesAndRepo;

    /**
     * @inheritDoc
     */
    public function createOtp(User $user): string {
        // generating random code for otp
        $otp = rand(100000, 999999);
        // updating the otp if already sent
        OtpCode::updateOrCreate([
            'user_id'  => $user->id,
            'otp_type' => config('usermanagement.constants.otp_code.otp_type.email'),
        ], [
            'code' => $otp,
        ]);
        return $otp;
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
    public function searchEntity(int $type, string $key, bool $filterAlreadyAttached = false): Collection {
        return $this->umRepo()->entityRepository->findByName(
            $key,
            $type,
            true,
            $filterAlreadyAttached
        );
    }

    /**
     * @inheritDoc
     */
    public function uploadUserAvatar($file): ?string {
        return $this->umServices()->fileService->uploadUserAvatar($file, true);
    }

    /**
     * @inheritDoc
     */
    public function updateUserEntity($userId, $data) {
        $this->umServices()->userService->updateUserEntity($userId, $data);
    }

    /**
     * @inheritDoc
     */
    public function updateUserVisibility($userId, $data) {
        $user = $this->umRepo()->userRepository->findById($userId);
        return $user->userVisibility()->updateOrCreate(
            ['user_id' => $userId],
            $data
        );
    }


    public function create($param) {
        //                $data = UserVisibility::create($param);
        return $this->userClmVisibility->create($param);
    }

    public function updateUserProfileField($userId, $field, $value) { //todo checkkkk
        //            $update = User::where('id', Auth::user()->id)->update([$request->field => $value]);
        return $this->userRepository->updateUserProfileField($userId, $field, $value);
    }

    public function deleteEntityUser($userId, $entityId) {
        //            $removeEntityUser = EntityUser::where('user_id', Auth::user()->id)
//                ->where('entity_id', $request->entity_id)
//                ->delete();
        return $this->entityUserRepository->deleteEntityUser($userId, $entityId);
    }

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the user by email
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param $emails
     * @return Collection|mixed
     */
    public function getUsersByEmail($emails) {
        return $this->umRepo()->userRepository->getUsersByEmail($emails);
    }

    public function getUserBadge($userId) {
        //        $user = User::with([
//            'unions',
//            'companies',
//            'instances',
//            'presses',
//            'facebookUrl',
//            'twitterUrl',
//            'instagramUrl',
//            'linkedinUrl',
//            'eventUsedTags',
//            'personalInfo',
//        ])->find($userId ? $userId : Auth::user()->id);
        return $this->userRepository->getUserBadge($userId);
    }

    public function findUserByIdentifier($identifier) {
//        User::where('identifier', $request->identifier)->count()
        return $this->userRepository->findUserByIdentifier($identifier);
    }

    public function updatePasswordByIdentifier($password) {
        //            User::where('identifier', $request->identifier)
//                ->update(['password' => Hash::make($request->password), 'identifier' => null]);
        return $this->userRepository->updatePasswordByIdentifier($password);
    }

    public function deleteProfilePic($userId) {
//        return User::where('id', $userId)->update(['avatar' => null]);
        $this->userRepository->deleteProfilePic($userId);
    }

    public function searchEntityByKey($val, $type, $userId) {
//        return Entity::where(function ($q) use ($val) { // todo Entity
//                $q->orWhere('long_name', 'LIKE', "%$val%");
//                $q->orWhere('short_name', 'LIKE', "%$val%");
//                $q->orWhere(DB::raw("CONCAT(`long_name`, ' ', `short_name`)"), 'LIKE', "%$val%");
//                $q->orWhere('entity_description', 'LIKE', "%$val%");
//            })
//                ->where('entity_type_id', $type)
//                ->whereDoesntHave('entityUser', function ($q) {
//                    $q->where('user_id', Auth::user()->id);
//                })->get();
        return $this->entityRepository->searchEntityByKey($val, $type, $userId);
    }

    public function updateProfile($userId, $param) {
        //        $update = User::where('id', Auth::user()->id)->update($param);
        return $this->userRepository->updateProfile($userId, $param);
    }

    /**
     * @inheritDoc
     */
    public function findByEmail($email): ?User {
        return $this->umRepo()->userRepository->findByEmail($email);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function createUser($data, int $groupId = null) {
        return $this->umServices()->userService->createUser($data, $groupId);
    }

    public function updateOrCreateEntityRelation($old, $new) {
        //        return EntityUser::updateOrCreate([
//            'entity_id' => $oldEntityId,
//            'user_id'   => Auth::user()->id
//        ], [
//            'entity_id'       => $newEntityId,
//            'entity_label'    => $position,
//            'created_by'      => Auth::user()->id,
//            'membership_type' => $memberType,
//        ]);

        return $this->entityUserRepository->updateOrCreateEntityRelation($old, $new);
    }

    public function updateOrCreateEntity($old, $new) {
        //        return Entity::updateOrCreate([
//            'long_name'      => $name,
//            'entity_type_id' => $type,
//            //'created_by'     => Auth::user()->id,
//        ], [
//            'long_name'      => $name,
//            'short_name'     => $name,
//            'entity_type_id' => $type,
//            'created_by'     => Auth::user()->id,
//        ]);

        return $this->entityRepository->updateOrCreateEntity($old, $new);
    }

    public function deleteEntityRelation($entityType, $userId) {
        //            return EntityUser::with(['entity' => function ($q) use ($entityType) { // todo entity User
//                $q->select("*");
//                $q->where('entity_type_id', $entityType);
//            }])->where('user_id', $userId)->whereHas('entity', function ($q) use ($entityType) {
//                $q->select("*");
//                $q->where('entity_type_id', $entityType);
//            })->delete();
        return $this->entityUserRepository->deleteEntityRelation($entityType, $userId);
    }

    public function deleteEntityByUserIdAndEntityId($userId, $entityId) {
//        return EntityUser::where('user_id', $userId)->whereIn('entity_id', $entityId)->delete();
        return $this->entityUserRepository->deleteEntityByUserIdAndEntityId($userId, $entityId);
    }

    public function findOrFailUser($userId) {
        //                $user = User::findOrFail($userId);
        return $this->userRepository->findOrFailUser($userId);
    }

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton Check the user by identifier
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param $identifier
     * @return mixed
     */
    public function checkUserByIdentifier($identifier) {
//        User::where('identifier', $request->identifier)->count();
        return $this->umRepo()->userRepository->checkUserByIdentifier($identifier);
    }

    public function updatePassword($identifier, $password) {
        //            User::where('identifier', $request->identifier)
//                ->update(['password' => Hash::make($request->password), 'identifier' => null]);
        return $this->userRepository->updatePassword($identifier, $password);
    }

    /**
     * @inheritDoc
     */
    public function updateUserById($id, $data) {
        $this->umRepo()->userRepository->update($id, $data);
    }

}
