<?php


namespace Modules\UserManagement\Services\BusinessServices\factory;


use Illuminate\Support\Facades\Auth;
use Modules\UserManagement\Services\BaseService;
use Modules\UserManagement\Services\BusinessServices\IKctService;
use Modules\UserManagement\Traits\ServicesAndRepo;
use Modules\UserManagement\Traits\UmHelper;

class KctService implements IKctService {
    use UmHelper;
    use ServicesAndRepo;

    /**
     * @inheritDoc
     */
    public function prepareUrl(string $type, array $data = []): ?string {
        $hostType = env("HOST_TYPE"); // getting the host type to append (https/http etc.)
        $frontAdminUrl = env("APP_ADMIN_FRONT"); // front side react url without sub-domain
        $hctUrl = env("APP_FRONT_HOST"); // hct side react url without sub-domain
        $accountName = $this->getSubdomain($data['fqdn'] ?? $this->umServices()->tenantService->getFqdn());
        switch ($type) {
            case 'dashboard':
                return "$hostType$accountName.$frontAdminUrl/{$data['groupKey']}/dashboard";
            case 'access':
                $token = $data['token'] ?? '';
                return "$hostType$accountName.$frontAdminUrl/access?token=$token";
            case 'set-password':
                return "$hostType$accountName.$frontAdminUrl/set-password";
            case 'signin' :
                return "$hostType$accountName.$frontAdminUrl/signin";
            case 'OTP' :
                return "$hostType$accountName.$hctUrl/quick-otp/{$data['param']}";
            case 'register' :
                return "$hostType$accountName.$hctUrl/quick-register";
            case 'HE-dashboard' :
                return "$hostType{$data['account']}/e/dashboard/{$data['event']}";
            case 'magic-link' :
                return "$hostType{$data['account']}/e/ml";
            case 'HE-page-expired':
                return "$hostType{$data['account']}/e/page-expired/{$data['event_uuid']}";
            default:
                return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function getSubDomainName($request) {
        $subDomain = explode('.', $request->getHost());
        if (count($subDomain) > 1) {
            $subDomain = $subDomain[0];
        } else {
            $subDomain = '';
        }
        return $subDomain;
    }

    /**
     * @inheritDoc
     */
    public function skipSetPasswordForAuth($email) {
        $user = Auth::user();
        $suUsers = $this->umServices()->superAdminService->getAllSuperAdmins();
        $suEmails = $suUsers->pluck('email')->toArray();
        $usersToSkip = array_merge([],$suEmails);
        // here given static value of user id because id of the user creating the account will always be 1
        $accCreatedByUser = $this->umRepo()->userRepository->findById(1);
        array_push($usersToSkip,$accCreatedByUser->email);
        $shouldSkip = in_array($email,$usersToSkip);
        if ($shouldSkip){
            if (!$user->login_count){ // checking if user is logged in previously
                $user->login_count = 1; // updating user as logged in
                $user->update();
            }
        }
    }

}
