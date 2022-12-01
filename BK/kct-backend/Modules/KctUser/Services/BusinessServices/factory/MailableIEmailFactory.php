<?php


namespace Modules\KctUser\Services\BusinessServices\factory;


use Exception;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Modules\KctUser\Events\UserForgetPassword;
use Modules\KctUser\Events\UserMagicLinkEvent;
use Modules\KctUser\Events\UserRegistered;
use Modules\KctUser\Events\UserBecomeModeratorEvent;
use Modules\KctUser\Events\UserInviteLinkEvent;
use Modules\Events\Entities\Event;
use Modules\KctUser\Events\BanUserEvent;
use Modules\KctUser\Exceptions\CustomValidationException;
use Modules\KctUser\Repositories\BaseRepo;
use Modules\KctUser\Services\BaseService;
use Modules\KctUser\Services\BusinessServices\IEmailService;
use Modules\KctUser\Services\KctCoreService;
use Modules\KctUser\Services\KctService;
use Modules\KctUser\Services\V2Services\DataV2Service;
use Modules\Events\Service\OrganiserService;
use Modules\KctUser\Traits\KctHelper;
use Modules\KctUser\Traits\Repo;
use Modules\KctUser\Traits\Services;
use Modules\UserManagement\Traits\ServicesAndRepo;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will be managing the email repository management
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class MailableIEmailFactory
 * @package Modules\KctUser\Services\BusinessServices\factory
 */
class MailableIEmailFactory implements IEmailService {
    use Services, Repo;
    use KctHelper;
    use ServicesAndRepo;

    /**
     * @inheritDoc
     */
    public function sendOtp(User $user,$eventUuid): string {
        $setting = [];
        $code = $this->userRepo()->userRepository->createOtp($user);
        $otpPageUrl = $this->umServices()->kctService->prepareUrl('register');
        $otpPageUrl = $otpPageUrl ."/" . $eventUuid .  "?fname=" . $user->fname . "&lname=" . $user->lname . "&email=" . $user->email;
        $account = request()->getHost();
        $encryptedUrlParamForApi = $this->encryptData($user->email);
        $apiUrl = env('HOST_TYPE') . $account . '/api/v1/p/verify/emailByLink?event_uuid=' . $eventUuid . '&email=' . $encryptedUrlParamForApi;
        $data = $setting;
        $data['code'] = $code->code;
        $data['url'] = $otpPageUrl;
        $data['apiUrl'] = $apiUrl;
        Mail::send('kctuser::email-templates.otp', $data, function (Message $message) use ($user, $data) {
            $message->to($user->email, "$user->fname $user->lname")
                ->subject($data['email_subject'] ?? 'OTP for your HumannConnect Account.')
                ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        });
        return $code;
    }

    /**
     * @return BaseService
     */
    public function getBaseService(): BaseService {
        return app(BaseService::class);
    }

    /**
     * @param $key
     * @return array|null
     * @throws Exception
     */
    private function getSetting($key): ?array {
        $setting = $this->userRepo()->settingRepository->getSettingByKey('email_graphics');
        $result['headerLogo'] = $this->userServices()->fileService->getFileUrl($setting->setting_value['headerLogo']);
        $result['footerLogo'] = $this->userServices()->fileService->getFileUrl($setting->setting_value['footerLogo']);
        return $result;
    }

    private function getTagsForOtp(User $user): array {
        return [
            '[[UserFname]]' => $user->fname,
        ];
    }

    /**
     * @param $setting
     * @param $tags
     * @return array
     */
    private function prepareDataAndApplyTags($setting, $tags): array {
        $setting['email_subject'] = $this->applyTags($setting['email_subject'], $tags);
        $setting['text_before_link'] = $this->applyTags($setting['text_before_link'], $tags);
        $setting['text_after_link'] = $this->applyTags($setting['text_after_link'], $tags);
        return $setting;
    }


    /******************************************************************************************************************/

    /**
     * @param $subject
     * @param $tags
     * @return array|string|string[]
     */
    private function applyTags($subject, $tags) {
        return str_replace(array_keys($tags), array_values($tags), $subject);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To send the ban user form event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @param $user
     * @param $request
     * @param array $tags
     * @return void
     */
    public function sendBanUserFromEvent($event, $user, $request, $tags = []) {
        if ($this->checkEmailEnabled()) {
            $tags['[[ParticipantLN]]'] = $user->lname;
            $tags['[[ParticipantFN]]'] = $user->fname;
            $auth = Auth::user();
            $textBeforeLink = "We inform you that this user : $user->fname $user->lname - $user->email " .
                "has been banned by space host $auth->fname $auth->lname for reason " .
                "";

            $textAfterLink = "During event : <br> <br>$event->title <br>" . Carbon::now()->toDateTimeString();
            $data = [
                'subject'  => "User has been banned: " . $user->email,
                'viewData' => [
                    'text_before_link' => $textBeforeLink,
                    'text_after_link'  => $textAfterLink,
                ],
            ];
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * To check emails are enabled or not for the current account
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    private function checkEmailEnabled(): bool {
        return true;
    }

    public function authorizeForMail() {
        $this->tenancy->website();
        $hostname = $this->tenancy->hostname();
        //        $acc_id = 1;
        if (isset($hostname->id)) {
            $acc_id = $hostname->id;
            return DB::connection('mysql')->table('account_settings')->where('account_id', $acc_id)->first(['email_enabled']);
        } else {
            return 1;
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To send invitation email containing link to login in the event.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @param $user
     *
     * @return mixed|null
     */
    public function sendInviteToExistingUser($event, $user): bool {
        if ($this->checkEmailEnabled()) {
            if ($event) {
                $link = $this->userServices()->kctService->getRedirectUrl(
                    request(), 'quick-login', ['EVENT_UUID' => $event->event_uuid]
                );
            } else {
                // if event not present remove event uuid and / from end of url
                $link = $this->userServices()->kctService->getRedirectUrl(
                    request(), 'quick-login', ['/EVENT_UUID' => '']
                );
            }
            $data = [
                'headerLogo'       => $this->umServices()->fileService
                    ->getFileLink(config('superadmin.constants.filePaths.emailHeaderLogo')),
                'footerLogo'       => $this->umServices()->fileService
                    ->getFileLink(config('superadmin.constants.filePaths.emailFooterLogo')),
                'text_before_link' => 'text_before_link',
                'text_after_link'  => 'text_after_link',
                'link'             => $link,
                'linkLabel'        => 'Login',
                'email_subject'    => 'email_subject',
            ];

            Mail::send('kctuser::email-templates.dynamic', $data, function (Message $message) use ($user, $data) {
                $message->to($user->email, "$user->fname $user->lname")
                    ->subject($data['email_subject'])
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            });

            return true;
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To send invitation email containing link to register in the event.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @param User $user
     * @return bool
     * @throws Exception
     */
    public function sendInvitationEmail($event, $user): bool {
        if ($this->checkEmailEnabled()) {
            $link = $this->userServices()->kctService->getRedirectUrl(
                request(), 'event-register', ['EVENT_UUID' => $event->event_uuid]
            );
            $data = [
                'headerLogo'       => '',
                'footerLogo'       => '',
                'text_before_link' => 'text_before_link',
                'text_after_link'  => 'text_after_link',
                'link'             => $link,
                'email_subject'    => 'You have been invited to a HumannConnect Event',
                'linkLabel'        => 'Register',
            ];

            Mail::send('kctuser::email-templates.dynamic', $data, function (Message $message) use ($user, $data) {
                $message->to($user->email, "$user->fname $user->lname")
                    ->subject($data['email_subject'])
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            });
            return true;
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function sendModeratorInfo($event, $user) {
        if ($this->checkEmailEnabled()) {
            $link = config('cocktail.bluejeans.account_sign_up');
            $data = [
                'subject'  => 'You Became a moderator of event',
                'viewData' => [
                    'text_before_link' => 'Congratulations !, You can now handle the event as moderator now',
                    'text_after_link'  => 'You will need the bluejeans account to proceed as moderator please click above link to register a free account or login with your own if you already own a bluejeans account',
                    'mail'             => [
                        'url'  => $link,
                        'name' => 'Create a free bluejeans account',
                    ]
                ]
            ];
            event(new UserBecomeModeratorEvent($data, $user->email));
        }
    }

    /**
     * @inheritDoc
     */
    public function sendVirtualRegistration($event, $userId, $data) {
        if ($this->checkEmailEnabled()) {
            $root = $data['root'];
            $tags = $data['tags'];
            if ($event && $event->type == 'virtual') {
//                $user = User::findOrFail($userId);
                $user = $this->baseService->userManagementService->findOrFailUser($userId);
                $setting = $this->getSetting('event_kct_registration', $user);
                $data = $this->prepareDataAndApplyTags($setting, $tags);
                $link = $this->prepareMagicLink($event, $user, $root);
                $data['viewData']['mail'] = [
                    'url' => $link,
                ];
                event(new UserMagicLinkEvent($data, $user->email));
            }
        }
    }

    /**
     * To prepare the magic link for the given user
     *
     * @param $event
     * @param $user
     * @param $root
     *
     * @return string
     */
    private function prepareMagicLink($event, $user, $root) {

        $queryString = http_build_query([
            'token'      => $user->createToken('check')->accessToken,
            'name'       => "$user->fname $user->lname",
            'event_uuid' => $event->event_uuid,
        ]);
        return "https://$root/ml?$queryString";
    }

    /**
     * @inheritDoc
     */
    public function sendForgetPassword($email, $rootLink) {
        if ($this->checkEmailEnabled()) {
            if (session()->has('lang')) {
                App::setLocale(strtolower(session('lang')));
            }

            $user = $this->userServices()->userManagementService->findByEmail($email);
            if (!$user) {
                throw new Exception('invalid_email');
            }
            $key = $this->randomString(36);
            $user->identifier = $key;
            $user->save();
            $encryptedEmail = $this->encryptData($email);
            $link = "https://$rootLink/kct-set-password/$encryptedEmail/$key";
            $data = [
                'subject'  => __('kctuser::message.reset_password'),
                'viewData' => [
                    'text_before_link' => 'There is an request generated to reset your password',
                    'text_after_link'  => 'You can reset the password by click on above link, if it was not you don\'t proceed your account is safe',
                    'link'             => $link,
                    'name'             => __('kctuser::message.reset_password'),
                    'headerLogo'       => $this->userServices()->fileService
                        ->getFileUrl(config('superadmin.constants.filePaths.emailHeaderLogo')),
                    'footerLogo'       => $this->userServices()->fileService
                        ->getFileUrl(config('superadmin.constants.filePaths.emailFooterLogo')),
                ]
            ];
            Mail::send('kctuser::email-templates.reset', $data['viewData'], function (Message $message) use ($user) {
                $message->to($user->email, "$user->fname $user->lname")
                    ->subject(__('kctuser::message.reset_password'));
                $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            });
        }
    }

    private function getRepo(): BaseRepo {
        return app(BaseRepo::class);
    }

    /**
     * @inheritDoc
     */
    public function sendEventRegSuccess($event) {
        $account = request()->getHost();
        $user = Auth::user();
        $dashboardUrl = $this->umServices()->kctService->prepareUrl('HE-dashboard', ['account' => $account, 'event' => $event->event_uuid]);
        $data = [
            'description'   => "Congrats! You have successfully registered yourself to the $event->title event.",
            'link'          => $dashboardUrl,
            'event_details' => [
                'name'  => $event->title,
                'start' => $event->start_time,
                'end'   => $event->end_time,
            ]
        ];
        Mail::send('kctuser::email-templates.event_reg_success', $data, function ($message) use ($user, $event) {
            $message->to($user->email, $user->email)->subject(__('kctuser::message.event_reg_success',
                ['event' => $event->title]));
            $message->from(env('MAIL_FROM_ADDRESS'), 'HumannConnect');
        });
    }

}
