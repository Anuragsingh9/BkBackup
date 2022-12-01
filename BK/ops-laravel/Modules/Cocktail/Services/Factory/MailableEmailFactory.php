<?php

namespace Modules\Cocktail\Services\Factory;

use App\Setting;
use App\Signup;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Cocktail\Events\UserBecomeModeratorEvent;
use Modules\Cocktail\Events\UserInviteLinkEvent;
use Modules\Cocktail\Events\UserMagicLinkEvent;
use Modules\Cocktail\Events\UserForgetPassword;
use Modules\Cocktail\Events\UserInvitedToEvent;
use Modules\Cocktail\Events\UserRegistered;
use Modules\Cocktail\Exceptions\CustomValidationException;
use Modules\Cocktail\Services\Contracts\EmailFactory;
use Modules\Cocktail\Services\KctService;
use Modules\Cocktail\Services\V2Services\KctCoreService;
use Modules\Events\Entities\Event;
use Modules\Events\Events\EventModified;
use Modules\Events\Events\EventReminderEvent;

class MailableEmailFactory implements EmailFactory {
    
    /**
     * @var bool
     */
    private $isEmailEnabled;
    
    public function sendIntRegistration($event, $userId, $tags) {
        if ($this->checkEmailEnabled() && $event && $event->type != config('events.event_type.virtual')) {
            $user = User::find($userId);
            $setting = $this->getSetting(config('cocktail.setting_keys.event_register'), $user);
            $data = $this->prepareDataAndApplyTags($setting, $tags);
            event(new UserInvitedToEvent($data, $user->email));
        }
    }
    
    public function sendVirtualRegistration($event, $userId, $data) {
        if ($this->checkEmailEnabled()) {
            $root = $data['root'];
            $tags = $data['tags'];
            if ($event && $event->type == 'virtual') {
                $user = User::findOrFail($userId);
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
     * @param Event $event
     * @param User $user
     * @return bool
     */
    public function sendInvitationEmail($event, $user) {
        if ($this->checkEmailEnabled()) {
            $tags = KctService::getInstance()->prepareEmailTags($event->event_uuid, $user);
            $tags["[[InviteeFN]]"] = $user->fname;
            $tags["[[InviteeLN]]"] = $user->lname;
            $tags["[[InviteeEmail]]"] = $user->email;
            $link = KctCoreService::getInstance()->getRedirectUrl(request(), 'event-register', ['EVENT_UUID' => $event->event_uuid]);
            $setting = $this->getSetting('event_kct_invite');
            $data = $this->prepareDataAndApplyTags($setting, $tags);
            $data['viewData']['mail'] = [
                'url' => $link,
            ];
            event(new UserInviteLinkEvent($data, $user->email));
            return true;
        }
        return true;
    }
    
    public function sendInviteToExistingUser($event, $user) {
        if ($this->checkEmailEnabled()) {
            if ($event) {
                $link = KctCoreService::getInstance()->getRedirectUrl(request(), 'quick-login', ['EVENT_UUID' => $event->event_uuid]);
                $tags = KctService::getInstance()->prepareEmailTags($event->event_uuid, $user);
                $tags["[[InviteeFN]]"] = $user->fname;
                $tags["[[InviteeLN]]"] = $user->lname;
                $tags["[[InviteeEmail]]"] = $user->email;
            } else {
                // if event not present remove event uuid and / from end of url
                $link = KctCoreService::getInstance()->getRedirectUrl(request(), 'quick-login', ['/EVENT_UUID' => '']);
                $tags = [];
            }
            $setting = $this->getSetting('event_kct_invite_existing');
            $data = $this->prepareDataAndApplyTags($setting, $tags);
            $data['viewData']['mail'] = [
                'url' => $link,
            ];
            event(new UserInviteLinkEvent($data, $user->email));
            return true;
        }
    }
    
    public function sendIntModification($event, $userId, $tags) {
        if ($this->checkEmailEnabled()) {
            $user = User::findOrFail($userId);
            $setting = $this->getSetting('event_modify', $user);
            $data = $this->prepareDataAndApplyTags($setting, $tags);
            event(new EventModified($data, $user->email));
        }
    }
    
    public function sendVirtualModification($event, $userId, $tags) {
        if ($this->checkEmailEnabled()) {
            $user = User::findOrFail($userId);
            $setting = $this->getSetting('event_kct_modification', $user);
            $data = $this->prepareDataAndApplyTags($setting, $tags);
            event(new EventModified($data, $user->email));
        }
    }
    
    public function sendOtp($user, $request = null, $eventUuid = null) {
        if ($this->checkEmailEnabled()) {
            $alreadySent = Signup::where('email', $user->email)->first();
            // if event id present prepare tags and redirect url according to event uuid
            if ($eventUuid) {
                $link = KctCoreService::getInstance()->getRedirectUrl($request, 'email_verify', ['EVENT_UUID' => $eventUuid]);
                $tags = KctService::getInstance()->prepareEmailTags($eventUuid, $user);
            } else {
                // if event not present remove event uuid and / from end of url
                $link = KctCoreService::getInstance()->getRedirectUrl($request, 'email_verify', ['/EVENT_UUID' => '']);
                $tags = [];
            }
            if ($alreadySent) {
                $code = $alreadySent->code;
            } else {
                $code = genRandomNum(6);
                Signup::insertGetId(['email' => $user->email, 'code' => $code]);
            }
            $setting = $this->getSetting(config('cocktail.setting_keys.validation_code'), $user);
            $data = $this->prepareDataAndApplyTags($setting, $tags);
            $data['viewData']['text_before_link'] .= "<h1>$code</h1>";
            $data['viewData']['mail'] = ['url' => $link];
            // send email here to otp
            event(new UserRegistered($data, $user->email));
        }
    }
    
    public function sendForgetPassword($email, $rootLink) {
        if ($this->checkEmailEnabled()) {
            if (session()->has('lang')) {
                App::setLocale(strtolower(session('lang')));
            }
            $user = User::where('email', $email)->first();
            if (!$user) {
                throw new CustomValidationException('invalid_email', null, 'message');
            }
            $key = generateRandomString(36);
            $user->identifier = $key;
            $user->save();
            $link = "https://$rootLink/kct-set-password/$email/$key";
            $data = [
                'subject'  => __('cocktail::message.reset_password'),
                'viewData' => [
                    'text_before_link' => 'There is an request generated to reset your password',
                    'text_after_link'  => 'You can reset the password by click on above link, if it was not you don\'t proceed your account is safe',
                    'mail'             => [
                        'url'  => $link,
                        'name' => __('cocktail::message.reset_password'),
                    ]
                ]
            ];
            event(new UserForgetPassword($data, $user->email));
        }
    }
    
    public function sendModeratorInfo($event, $user) {
        if ($this->checkEmailEnabled()) {
            $link = config('cocktail.bluejeans.account_sign_up');
            $data = [
                'subject'  => 'You Become a moderator of event',
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
    
    public function sendReminderEmailToEvent($reminderKey, $tags, $users) {
        if ($this->checkEmailEnabled()) {
            foreach ($users as $user) {
                $setting = $this->getSetting($reminderKey, $user);
                if ($setting) {
                    $tags['[[ParticipantLN]]'] = $user->lname;
                    $tags['[[ParticipantFN]]'] = $user->fname;
                    $data = $this->prepareDataAndApplyTags($setting, $tags);
                    event(new EventReminderEvent($data, $user->email));
                }
            }
        }
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * To check emails are enabled or not for the current account
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    private function checkEmailEnabled() {
        if ($this->isEmailEnabled === null) {
            $core = app(\App\Http\Controllers\CoreController::class);
            $authorizeForMail = $core->authorizeForMail();
            if (!isset($authorizeForMail->email_enabled) || $authorizeForMail->email_enabled == 0) {
                $this->isEmailEnabled = false;
            }
            $this->isEmailEnabled = true;
        }
        return $this->isEmailEnabled;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * To get the setting element from db according to provided user language setting
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param      $key
     * @param null $user
     *
     * @return mixed|null
     */
    private function getSetting($key, $user = null) {
        $lang = 'FR';
        $user = $user ? $user : Auth::user();
//        check lang in user setting
        if ($user && $user->setting) {
            $setting = json_decode($user->setting, 1);
            $lang = isset($setting['lang']) ? $setting['lang'] : $lang;
        } else if (isset($_SESSION['lang'])) { // check lang in session
            $lang = $_SESSION['lang'];
        }
        $key = "{$key}_{$lang}";
        $data = Setting::where('setting_key', $key)->first();
        return ($data) ? json_decode($data->setting_value, JSON_OBJECT_AS_ARRAY) : null;
    }
    
    /**
     * To apply the tags on setting from tags array => value pair
     *
     * @param $subject
     * @param $tags
     *
     * @return string|string[]
     */
    private function applyTags($subject, $tags) {
        return str_replace(array_keys($tags), array_values($tags), $subject);
    }
    
    /**
     * To prepare the email template and apply the tags on data
     *
     * @param array $setting
     * @param array $tags
     *
     * @return array
     */
    private function prepareDataAndApplyTags($setting, $tags) {
        return [
            'subject'  => isset($setting['email_subject']) ? $this->applyTags($setting['email_subject'], $tags) : '',
            'viewData' => [
                'text_before_link' => isset($setting['text_before_link']) ? $this->applyTags($setting['text_before_link'], $tags) : "",
                'text_after_link'  => isset($setting['text_after_link']) ? $this->applyTags($setting['text_after_link'], $tags) : "",
            ],
        ];
    }
    
    /**
     * To prepare the magic link for the given user
     *
     * @param Event $event
     * @param User $user
     * @param string $root
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
}