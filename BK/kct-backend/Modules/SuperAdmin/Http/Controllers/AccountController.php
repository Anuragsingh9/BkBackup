<?php

namespace Modules\SuperAdmin\Http\Controllers;

use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Modules\SuperAdmin\Entities\GroupUser;
use Modules\SuperAdmin\Http\Requests\CreateAccountOtpVerifyRequest;
use Modules\SuperAdmin\Http\Requests\CreateAccountStep1Request;
use Modules\SuperAdmin\Http\Requests\CreateAccountStep2Request;
use Modules\SuperAdmin\Http\Requests\InstantAccountCreateRequest;
use Modules\SuperAdmin\Http\Requests\UpdateAccountSettings;
use Modules\SuperAdmin\Traits\SuHelper;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will manage the tenant account related functionalities
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class AccountController
 * @package Modules\SuperAdmin\Http\Controllers
 */
class AccountController extends BaseController {
    use SuHelper;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To process the step 1 of sign up in which there will be email will be registered with the otp
     * An email will sent to target email-id with containing OTP
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param CreateAccountStep1Request $request
     * @return RedirectResponse
     */
    public function signupStep1(CreateAccountStep1Request $request): RedirectResponse {
        // Check the user is exists is already or not
        $isEmailExists = $this->repo->organisationRepository->findByEmail($request->input("email"));
        $isSuperAdminExists = $this->repo->superAdminRepository->getUserByEmail($request->input("email"));
        // Check is user is already exists and is user super admin
        if ($isEmailExists || $isSuperAdminExists) {
            return redirect()->back()->withErrors([
                'isEmailExists' => __('validation.unique', ['attribute' => 'email']),
            ]);
        }
        $this->services->tempDataService->put(['signup_email' => $request->input('email')]);
        $otp = $this->repo->otpRepository->createOtp($request->input('email'));
        $this->services->emailService->sendSuOtp($otp->code, $request->input('email'));
        return redirect()->route('su-account-create-1-2', ['otp' => $otp]);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the otp page
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return View|RedirectResponse
     */
    public function getOtpPage() {
        if (session()->has('signup_email')
            && $otp = $this->repo->otpRepository->getOtpByEmail(session('signup_email'))) {
            return view('superadmin::auth.signup.signup_step_1_verify_email', ['otp' => $otp]);
        }
        return redirect()->route('su-account-create-1');
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To resent the otp
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return RedirectResponse
     */
    public function otpResend(): RedirectResponse {
        $email = $this->services->tempDataService->get('signup_email');
        $otp = $this->repo->otpRepository->createOtp($email);
        $this->services->emailService->sendSuOtp($otp->code, $email);
        return redirect()->back()->with([
            'messages' => [__("superadmin::messages.otp_resend")],
            'otp'      => $otp,
        ]);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To verify if the user submitted otp is correct
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param CreateAccountOtpVerifyRequest $request
     * @return RedirectResponse
     */
    public function verifyEmail(CreateAccountOtpVerifyRequest $request): RedirectResponse {
        $code = $request->input('code_1', '');
        $code .= $request->input('code_2', '');
        $code .= $request->input('code_3', '');
        $code .= $request->input('code_4', '');
        $code .= $request->input('code_5', '');
        $code .= $request->input('code_6', '');

        $email = $this->services->tempDataService->get('signup_email');

        if ($code && $email) {
            $sentOtp = $this->repo->otpRepository->getOtpByEmail($email);
            // if otp didn't send and user entered here manually
            if (!$sentOtp) {
                redirect()->route('su-account-create-1')->withErrors([
                    'message' => __('superadmin::messages.register_to_get_otp')
                ]);
            }

            // if code match with valid code then redirect to next page
            if ($sentOtp->code == $code) {
                return redirect()->route('su-account-create-2');
            }
        }
        return back()->withErrors(['otp' => __('superadmin::messages.otp_invalid')]);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @description To perform the second step of account creation
     * - create organiser
     * - actual database tenant will be created
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param CreateAccountStep2Request $request
     * @return RedirectResponse
     */
    public function signupStep2(CreateAccountStep2Request $request): RedirectResponse {
        try {
            $fqdn = $request->input('fqdn') . "." . env("APP_HOST");
            $email = session()->get('signup_email');
            $this->services->accountService->createAccount(
                $request->input('fqdn'),
                $request->input('organisation_name'),
                $request->input('first_name'),
                $request->input('last_name'),
                $email,
                $request->input('password')
            );
            $user = $this->services->userManagement->findUserByEmail($email);
            Auth::loginUsingId($user->id);
            session()->forget('signup_email');
            return redirect($this->services->accountService->prepareUrl('access', [
                'fqdn'  => $fqdn,
                'token' => $user->createToken('check')->accessToken,
            ]));
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the account list page
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return View
     */
    public function accountList(): View {
        $hostnames = $this->repo->accountRepository->getAllHostnames();
        return view('superadmin::account.list')->with(['hostnames' => $hostnames]);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the account settings
     * - Get the conference setting
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $accountId
     * @return View|RedirectResponse
     */
    public function getAccountSetting($accountId) {
        $website = $this->repo->accountRepository->getWebsiteByHostnameId($accountId);
        if ($website) {
            // setting current environment to tenant
            $this->tenant->tenant($website);
            // account settings to display
            $accountSettings = $this->services->adminService->getAccountSetting();
            // conference settings to display
            $conferenceSettings = $this->services->adminService->getConferenceSetting();
            // all the fqdn attached to tenant website
            $fqdns = $this->tenant->tenant()->hostnames()->pluck('fqdn')->toArray();
            $superGroup = $this->services->adminService->fetchSuperGroup($accountId);
            //get sub domain name
            $subDomainUrl = $this->getSubdomain($fqdns[0]);
            //get domain name
            $domainUrl = env("HOST_TYPE") . $fqdns[0] . "/oit/dashboard";
            $technicalSettingUrl = route(
                'su-account-access',
                [
                    'hostnameId' => $accountId,
                    'redirectUrl' => "technicalUrl"
                ]
            );
            if ($accountSettings && $conferenceSettings) {
                return view('superadmin::account.account_setting')->with(compact(
                    'accountSettings',
                    'conferenceSettings',
                    'fqdns',
                    'accountId',
                    'technicalSettingUrl',
                    'domainUrl',
                    'subDomainUrl',
                    'superGroup'
                ));
            }
        }
        return back();
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the account and conference settings
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param UpdateAccountSettings $request
     * @return RedirectResponse
     */
    public function updateAccountSetting(UpdateAccountSettings $request): RedirectResponse {
        $accountId = $request->input('accountId');
        // setting hostname before updating setting
        $this->services->tenantService->setWebsiteByHostId($accountId);
        $accountSettings = $this->services->adminService->getAccountSetting();
        $maxGroup = array_key_exists('max_group_limit',$accountSettings) ? $accountSettings['max_group_limit'] : 1;
        $accountSettings = [
            'events_enabled'               => (int)$request->input('events_enabled', 1),
            'kct_enabled'                  => (int)$request->input('kct_enabled', 1),
            'conference_enabled'           => (int)$request->input('conference_enabled', 1),
            'allow_multi_group'            => (int)$request->input('allow_multi_group', 0),
            'max_group_limit'              => (int)$request->input('max_group_allowed', $maxGroup),
            'allow_user_to_group_creation' => (int)$request->input('allow_user_to_create_group', 0),
            'group_analytics'              => (int)$request->input('group_analytics', 0),
            'event_analytics'              => (int)$request->input('event_analytics', 0),
            'acc_analytics'                => (int)$request->input('acc_analytics', 0),
            'all_day_event_enabled'        => (int)$request->input('all_day_event_enabled', 1),
        ];
        $conferenceSettings = [
            // 1 For Zoom, @see above `conference_type`
            'current_conference' => $request->input('event_conference_type') == 1 ? 1 : 2,
            'bluejeans'          => [
                'app_key'           => $request->input('event_bluejeans_client_id'),
                'app_secret'        => $request->input('event_bluejeans_client_secret'),
                'app_email'         => $request->input('event_bluejeans_client_email'),
                'number_of_license' => $request->input('event_bluejeans_licenses'),
            ],
            'zoom'               => [
                'app_key'           => $request->input('event_zoom_key'),
                'app_secret'        => $request->input('event_zoom_secret'),
                'app_email'         => $request->input('event_zoom_email'),
                'number_of_license' => $request->input('event_zoom_licenses'),
            ],
        ];
        $this->services->adminService->updateAccountSetting($accountSettings);
        $this->services->adminService->updateConfSetting($conferenceSettings);
        return back();
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create an account instantly
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param InstantAccountCreateRequest $request
     * @return JsonResponse
     */
    public function instantAccountCreate(InstantAccountCreateRequest $request): JsonResponse {
        try {
            DB::beginTransaction();
            // fetching it before because after account create the tenant will be changed to respective hostname
            // so Auth::user will point to tenant users table so fetching it now.
            $staffUser = Auth::user();

            $hostname = $this->services->accountService->createAccount(
                $request->input('accName'),
                $request->input('orgName'),
                $request->input('orgFname'),
                $request->input('orgLname'),
                $request->input('orgEmail')
            );

            // Prepare account organisation data
            $userData = $this->services->accountService->prepareAccountOrgData(
                $staffUser->fname,
                $staffUser->lname,
                $staffUser->email
            );

            // creating current support staff as organiser admin as well here
            $this->services->userManagement->createUser(
                $userData,
                null,
                GroupUser::$role_Organiser
            );

            DB::commit();
            return response()->json([
                'status' => true,
                'fqdn'   => $request->input('accName') . '.' . env('HOST_SUFFIX'),
                'url'    => route('su-account-access', ['hostnameId' => $hostname->id]),
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->handleIse($e);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if the account name is present or not.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function accountNameCheck(Request $request): RedirectResponse {
        $hostname = $this->repo->accountRepository->findHostnameByName($request->input('accountName'));
        if ($hostname) {
            $url = $this->services->userManagement->prepareUrl('signin', ['fqdn' => $hostname->fqdn]);
            return redirect()->to($url);
        }
        return redirect()->back()->withErrors([
            'accountName' => [
                __("validation.exists", ['attribute' => 'Account Name']),
            ]
        ]);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method used for if the user forget the account name
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function accountNameForget(Request $request): RedirectResponse {
        $organisation = $this->repo->organisationRepository->findByEmail($request->input('email'));
        if (!$organisation) {
            return redirect()->back()->withErrors([
                'email' => [__("validation.exists", ['attribute' => 'email'])]
            ]);
        }
        // send the account reset link to to user email
        $this->services->emailService->sendAccountReset($organisation);
        return redirect()->route('index')->with('messages', [
            __("superadmin::messages.reset_account_sent"),
        ]);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method used for set the language of application
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $locale
     * @return RedirectResponse
     */
    public function setLang($locale) {
        App::setLocale($locale);
        session()->put('locale', $locale);
        return redirect()->back();
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To access the account by hostname id
     * This will create a user in respective account if the user doesn't exist
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $hostnameId
     * @return RedirectResponse
     */
    public function access($hostnameId, $redirectUrl = null): RedirectResponse {
        try {
            $superAdminUser = Auth::user();
            $hostname = $this->services->tenantService->setTenantByHostnameId($hostnameId);
            // checking if current super admin user already present in account or not
            $user = $this->services->userManagement->findUserByEmail($superAdminUser->email, true);
            if($user && $user->trashed()) {
                $user->restore();
            }
            if (!$user) {
                DB::connection('tenant')->beginTransaction();
                $userData = $this->services->accountService->prepareAccountOrgData(
                    $superAdminUser->fname,
                    $superAdminUser->lname,
                    $superAdminUser->email
                );
                $roles = $this->services->userManagement->getRoles();
                // add super admin role in user data
                $userData['roles'] = [$roles['super_admin']];
                $user = $this->services->userManagement->createUser(
                    $userData,
                    null,
                    GroupUser::$role_Organiser
                );
                DB::connection('tenant')->commit();
            }
            // if web route have redirectUrl parameter then prepare the information for send
            if($redirectUrl){
                $dataToSend = [
                    'fqdn'  => $hostname->fqdn,
                    'token' => $user->createToken('check')->accessToken,
                    'redirect' => $redirectUrl,
                ];
            }
            else{
                $dataToSend = [
                    'fqdn'  => $hostname->fqdn,
                    'token' => $user->createToken('check')->accessToken,
                ];
            }
//            $user->assignRole('super_admin');
            return redirect($this->services->accountService->prepareUrl('access',
                $dataToSend,
            ));
        } catch (Exception $e) {
            DB::connection('tenant')->rollBack();
            return redirect()->back()->withErrors($e->getMessage());

//            return redirect()->back();
        }
    }
}
