<?php


namespace Modules\UserManagement\Services\BusinessServices\factory;


use Illuminate\Support\Facades\Mail;
use Modules\UserManagement\Services\BaseService;
use Modules\UserManagement\Services\BusinessServices\IEmailService;
use Modules\UserManagement\Services\UserHelper;
use Modules\UserManagement\Traits\ServicesAndRepo;

class EmailService  implements IEmailService {

    use ServicesAndRepo;
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the singleton BaseService Object
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return BaseService
     */
    private ?BaseService $baseService = null;
    private $services;

    public function getBaseService(): BaseService {
        if (!$this->services) {
            $this->services = app(BaseService::class);
        }
        return $this->services;
    }

    /**
     * @inheritDoc
     */
    public function resetPassword($request){
        $to_name = $request->email;
        $to_email = $request->email;
        $subDomain = $this->umServices()->kctService->getSubDomainName($request);
        $domain = env("APP_HOST");
        $key = UserHelper::randomString();
        $user = $this->umRepo()->userRepository->findByEmail($request->email);
        $user->identifier = $key;
        $user->save();
        $data = array_merge([
            'link'        => env("HOST_TYPE") . "$subDomain.". env("APP_ADMIN_FRONT") . "/reset-view?email=$to_email&i=$key",
        ],$this->getBasicData());

        // Send the reset password link to user email
        Mail::send('usermanagement::email-templates.forgot_pwd', $data, function ($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)->subject('Password Reset Request');
            $message->from(env('MAIL_FROM_ADDRESS'), 'HumannConnect');
        });
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the basic data for the email template
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    private function getBasicData(): array {
        return [
            'headerLogo' => $this->umServices()->fileService
                ->getFileUrl(config('superadmin.constants.filePaths.emailHeaderLogo')),
            'footerLogo' => $this->umServices()->fileService
                ->getFileUrl(config('superadmin.constants.filePaths.emailFooterLogo')),
        ];
    }

    /**
     * @inheritDoc
     */
    public function sendWelcomeEmail($user, $groupId, $groupRole) {
        $group = $this->umServices()->adminService->getGroupData($groupId);
        $adminRoles = [2 => 'Pilot', 3 => 'Owner'];
        $role = $adminRoles[$groupRole];
        $link = $this->umServices()->kctService->prepareUrl('signin');

        $data = [
            'group'      => $group->name,
            'role'       => $role,
            'signInLink' => $link,
            'email'      => $user->email,
            'password'   => $user->email,
        ];
        Mail::send('usermanagement::email-templates.welcome_email', $data, function ($message) use ($user) {
            $message->to($user->email, $user->fname)->subject(__('usermanagement::messages.welcome_to_HCT'));
            $message->from(env('MAIL_FROM_ADDRESS'), 'HumannConnect');
        });
    }

}
