<?php


namespace Modules\KctUser\Services\BusinessServices\factory;

use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\KctUser\Exceptions\CustomValidationException;
use Modules\KctUser\Services\BaseService;
use Modules\KctUser\Services\BusinessServices\IKctUserService;
use Modules\KctUser\Services\HelperService;
use Modules\KctUser\Traits\KctHelper;
use Modules\KctUser\Traits\Repo;
use Modules\KctUser\Traits\Services;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will be managing the user services
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface KctUserService
 * @package Modules\KctUser\Services\BusinessServices\factory
 */
class KctUserService implements IKctUserService {
    use Services, Repo, KctHelper;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the singleton BaseService Object
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return BaseService
     */
    private ?BaseService $baseService = null;

    public function getBaseService(): BaseService {
        if (!$this->baseService) {
            $this->baseService = app(BaseService::class);
        }
        return $this->baseService;
    }

    /**
     * -------------------------------------------------------------------------------------------------------------
     * @description to prepare the hash code for the user
     * -------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function prepareHashCode() {
        $tenancy = app(\Hyn\Tenancy\Environment::class);
        $hostname = $tenancy->hostname();
        $hostCode = DB::connection('mysql')->table('hostname_codes')->where(['fqdn' => $hostname->fqdn])->first(['hash']);
        $randCode = $this->baseService->helperService->generateRandomValue(3);
        return $this->baseService->helperService->setPasscode($hostCode->hash, $randCode);
    }

    /**
     * @param $request
     * @param int $throwEmailExists
     * @param array $data
     * @return JsonResponse
     * @throws CustomValidationException
     */
    public function register($request, $throwEmailExists = 1, $data = []) {
//        $user = User::where('email', $request->email)->first();
        $user = $this->baseService->userManagementService->findByEmail($request->email); // todo
        if ($user) {
            if ($throwEmailExists)
                throw new CustomValidationException(__('validation.exists', ['attribute' => 'email']));
            else
                return response()->json([
                    'status' => true,
                    'data'   => [
                        'already_exists' => true,
                        'fname'          => $user->fname,
                        'lname'          => $user->lname,
                    ]], 200);
        }
        if (empty($data)) {
            $data = [
                'email'    => $request->email,
                'fname'    => $request->fname,
                'lname'    => $request->lname,
                'password' => bcrypt($request->email),
                'on_off'   => 0, // as user is not verified yet,
            ];
        }
//        $user = User::create($data);
        $user = $this->baseService->userManagementService->createUser($data); // todo
        if (!$user)
            throw new Exception();
        return $user;
    }

    /**
     * @param $otp
     * @param null $user
     * @return bool|\Illuminate\Contracts\Auth\Authenticatable|null
     * @throws CustomValidationException
     */
    public function otpVerify($otp, $user = null) {
        $user = $user ? $user : Auth::user();
//        $sentOtp = Signup::where('email', $user->email)->first();
        $sentOtp = $this->baseService->superAdminService->getOtp($user->email); //todo SignUp
        if ($sentOtp && $otp == $sentOtp->code) {
//            $validate = User::where('id', Auth::user()->id)->update(['on_off' => 1]);
            $param = ['on_off' => 1];
            $validate = $this->baseService->userManagementService->updateProfile(Auth::user()->id, $param); // todo user
            if (!$validate) throw new Exception();
            return $user;
        } else {
            throw new CustomValidationException();
        }

    }

    /**
     * @inheritDoc
     */
    public function getUserBadge($userId, $eventUuid = null): ?User {
        $user = $this->userServices()->userManagementService->findById($userId);
        //getting the user related tags
        $tag = $this->getUserTag($user, $eventUuid);
        if (!$user) {
            throw new Exception('Invalid user to get');
        }
        $user->tag = $tag;
        if ($eventUuid) {
            $user->load(['eventUser' => function ($q) use ($eventUuid) {
                $q->where('event_uuid', $eventUuid);
            }]);
        }
        $user->load('company');
        $user->load('unions');

        return $this->userServices()->dataMapServices->loadPPTagsForUser($user);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description get user tags
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param User|null $user
     * @param null $eventUuid
     * @return array[]
     */
    public function getUserTag(?User $user, $eventUuid = null): array {
        $used_tag = $this->userRepo()->orgTagUserRepository->getExistingTag($user, true, $eventUuid);
        $unused_tag = $this->userRepo()->orgTagUserRepository->getExistingTag($user, false, $eventUuid);
        return ['used_tag' => $used_tag, 'unused_tag' => $unused_tag];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will check
     * 1. if image w and h is less than 300 then it will find the max dimension an resize the image with
     *    max dimension w and h.
     * 2. if image w and h is equal or more than  300 then it will resize the image to 300*300
     *
     * @note image will become square as w and h will be same.
     * -----------------------------------------------------------------------------------------------------------------
     * @inheritDoc
     */
    public function prepareAvatar($image) {
        $userVal = config('kctuser.validations.user');
        $imgWidth = $image->width();
        $imgHeight = $image->height();
        // image w and h is less than 300
        if ($imgWidth < $userVal['avatar_max_width'] || $imgHeight < $userVal['avatar_max_height']) {
            $maxDimension = max([$imgWidth, $imgHeight]);
            return $image->resize($maxDimension, $maxDimension)->stream();
        }
        // image w and h is more than 300
        return $image->resize($userVal['avatar_max_width'], $userVal['avatar_max_height'])->stream();
    }


}
