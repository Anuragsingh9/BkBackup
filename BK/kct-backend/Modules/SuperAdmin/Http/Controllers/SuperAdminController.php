<?php

namespace Modules\SuperAdmin\Http\Controllers;

use App\Models\User;
use Exception;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Modules\KctAdmin\Entities\Group;
use Modules\SuperAdmin\Entities\DbDeleteLog;
use Modules\SuperAdmin\Entities\OtpCode;
use Modules\SuperAdmin\Entities\SuperAdminUser;
use Modules\SuperAdmin\Http\Requests\SUForgotPasswordRequest;
use Modules\SuperAdmin\Http\Requests\SuperAdminLoginRequest;
use Modules\SuperAdmin\Http\Requests\SUResetPasswordRequest;
use Spatie\DbDumper\Databases\MySql;


/**
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will manage the user authentication related functionality
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class SuperAdminController
 * @package Modules\SuperAdmin\Http\Controllers
 */
class SuperAdminController extends BaseController {


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method use for initiate the application
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return Application|Factory|View|RedirectResponse
     */
    public function index(Request $request) {
        $hostname = $request->getHost();
        $tenant = $this->services->tenantService->getHostname();
        $publicSubDomain = config('superadmin.public_subDomain') . '.' . env('APP_HOST');
        if (!$tenant) {// no tenant found
            if ($hostname == env('APP_HOST')) {
                // as subdomain account not exists, and url does not have any sub domain then return the index view
                return view('superadmin::index');
            } elseif ($hostname == $publicSubDomain) {
                // if the account is specific then return the index view
                return view('superadmin::index');
            } else {
                return redirect()
                    ->to(
                        env("HOST_TYPE")
                        . $publicSubDomain
                        . route('index', [], false)
                    );
            }
        }
        $hostType = env("HOST_TYPE");
        $url = "$hostType$hostname/oit/signin";
        return Redirect::to($url);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method use for super admin signin
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param SuperAdminLoginRequest $request
     * @return RedirectResponse
     */
    public function superAdminSignin(SuperAdminLoginRequest $request): RedirectResponse {
        try {
            $user = SuperAdminUser::where(['email' => $request->input('email')])->first();
            if ($user != NULL && Hash::check($request->input('password'), $user->password)) {
                Auth::loginUsingId($user->id);
                return redirect()->route('su-account-list');
            } else {
                return back()->withErrors(['email' => 'Invalid Credentials']);
            }
        } catch (Exception $e) {
            DB::rollback();
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method use for logout user from application
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return RedirectResponse
     */
    public function logout() {
        Auth::logout();
        return redirect()->route('su-signin');
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method use for forget password to recover the user password
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param SUForgotPasswordRequest $request
     * @return JsonResponse|RedirectResponse
     */
    public function forgotPassword(SUForgotPasswordRequest $request) {
        try {
            $link = $this->services->tenantService->getHost($request);
            $otpCode = $this->repo->otpRepository->createOtp($request->email);
            $this->services->emailService->forgotPassword($link, $otpCode->code, $request->email);
            return redirect()->back()->with('msg', __('superadmin::messages.sent_pwd_reset_link'));
        } catch (Exception $e) {
            return response()->json(['status' => true, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method use for redirect to reset view page
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $email
     * @param $key
     * @return Application|Factory|View
     */
    public function resetView($email, $key) {
        return view('superadmin::auth.reset_password', compact('email', 'key'));
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method use for reset the user password
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param SUResetPasswordRequest $request
     * @return JsonResponse|RedirectResponse
     */
    public function resetPassword(SUResetPasswordRequest $request) {
        try {
            // get the valid otp from db
            $user = OtpCode::where('email', $request->email)->first();
            $otp = $user->code;
            if (Hash::check($otp, $request->identifier)) {
                $suUser = $this->repo->superAdminRepository->getUserByEmail($request->email);
                $suUser->update(['password' => Hash::make($request->password)]);
                return redirect()->route('su-signin');
            }
            return redirect()->back();
        } catch (Exception $e) {
            return response()->json(['status' => true, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method use for get the general settings
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return Application|Factory|View
     */
    public function getGeneralSettings() {
        $data = $this->repo->superAdminRepository->getSettingByKey('public_video');
        $imagePath = isset($data->setting_value['image_path']) && $data->setting_value['image_path']
            ? $data->setting_value['image_path']
            : config('superadmin.constants.video_explainer_default_image');
        $path = $this->services->fileService->getFileUrl($imagePath, false);
        $settings = array_merge($data->setting_value, ['image_path' => $path]);
        return view('superadmin::general-settings.index')->with(compact('settings'));
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method use for set the general settings
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function setGeneralSettings(Request $request): JsonResponse {
        $request->validate([
            'public_video.image_path' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // if image path have value then set the image path
        if ($request['public_video.image_path']) {
            $path = config('superadmin.constants.s3.video_explainer_image');
            $file = $this->services->fileService->storeFile(
                $request->file('public_video.image_path'),
                $path,
                false
            );
        } else {
            $file = config('superadmin.constants.video_explainer_default_image');
        }

        $dataToInsert = array_merge($request->input('public_video'), ['image_path' => $file]);

        // if request have public video
        if ($request->has('public_video')) {
            $settings = $this->repo->superAdminRepository->getSettingByKey('public_video');
            $settings->setting_value = $dataToInsert;
            $path = $this->services->fileService->getFileUrl($settings->setting_value['image_path'], false);
            $settings->update();

            $settingValue = $settings->setting_value;
            $settingValue['image_path'] = $path;
            $settings->setting_value = $settingValue;
        }

        return response()->json(['status' => true, 'data' => $settings]);
    }

    private Hostname $hostname;
    private Website $website;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method use for delete the account
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @param $accountId
     * @return JsonResponse|RedirectResponse
     */
    public function deleteAccount(Request $request, $accountId) {
        try {

            // find the tenant database uuid
            $hostname = Hostname::find($accountId);
            $website = $hostname->website;
            $databaseName = $website->uuid;

            // export file from database
            $exportFileName = "$databaseName.sql";
            $exportFullPath = base_path(). "/deleted_db/$exportFileName";

            $dbExport = MySql::create();
            if (env('DUMP_BINARY_PATH')) {
                $dbExport = $dbExport->setDumpBinaryPath(env("DUMP_BINARY_PATH"));
            }
            $dbExport->setDbName($databaseName)
                ->setHost(env("DB_HOST"))
                ->setUserName(env("DB_USERNAME"))
                ->setPassword(env("DB_PASSWORD"))
                ->dumpToFile("$exportFullPath");

            $s3Path = "deleted_db/$exportFileName";

            // store file path in s3
            Storage::disk('s3')->put(
                $s3Path,
                file_get_contents($exportFullPath)
            );

            DbDeleteLog::create([
                'fqdn'         => $hostname->fqdn,
                'db_name'      => $databaseName,
                'db_file_path' => $s3Path,
            ]);

            $hostname->forceDelete();
            $website->forceDelete();

            return redirect()->back();
        } catch (Exception $exception) {
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 500);
        }

    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to redirect to hct application
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @param $dns
     * @return Application|Factory|View|RedirectResponse
     */
    public function redirectToHct(Request $request, $dns) {
        $host = env('APP_HOST');
        $fqdn = $dns . '.' . $host;
        $hostname = Hostname::where('fqdn', $fqdn)->first();
        if ($hostname) {
            $website = Website::find($hostname->website_id);
            if ($website) {
                $this->services->tenantService->setTenantByWebsite($website);
                $events = $this->services->adminService->getGroupEvent();
                $eventCount = $events->count();
                if ($eventCount > 0) {
                    if ($eventCount > 1) {
                        // here fetched events are in ascending order
                        // so when event have same start_time then 0 index will contain the newly created event
                        $firstEvent = $events[0]; // newly created
                        $secondEvent = $events[1]; // firstly created
                        // if two events have same start time then select the event which is created first
                        $nextEvent = $firstEvent->start_time == $secondEvent->start_time ? $secondEvent : $firstEvent;
                    } else {
                        $nextEvent = $events->first();
                    }
                    $url = env('HOST_TYPE') . $hostname->fqdn . "/e/quick-login/" . $nextEvent->event_uuid;
                    return redirect()->to($url);
                }
                return view('superadmin::account_event', compact("fqdn"));
            }
        }
        return \redirect()->back();
    }

}
