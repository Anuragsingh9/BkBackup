<?php

namespace App\Http\Controllers;
use App\BulkAccAdmin;
use App\Console\Commands\CreateAccount;
use App\Exceptions\CustomException;
use App\Exceptions\CustomValidationException;
use App\Http\Requests\BulkAccCreateRequest;
use App\Project;
use App\Services\MeetingService;
use App\Services\OrganisationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;
use Modules\Crm\Entities\TranscribeTracking;
use Modules\Newsletter\Entities\AdobePhotosTracking;
use App\Services\SuperAdmin;
use App\Setting;
use App\Workshop;
use Illuminate\Http\Request;
use App\WorkshopMeta;
//use Modules\Newsletter\Model\Newsletter;
use Modules\Qualification\Entities\QualificationReminder;
use Modules\Qualification\Entities\QualificationTemplate;
use Redirect,
    Session,
    Validator;
use Illuminate\Support\Facades\Auth,
    Hash,
    DB;
use App\StartCategory;
use App\SuperadminLogin;
use App\AccountAccessKey,
    App\AccountSettings;
use App\Hostname as HostnameModel;
use App\User;
use App\Organisation;
use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;
use Hyn\Tenancy\Environment;
use App\Guide;
use App\Grdp;
use Illuminate\Support\Facades\Storage;

    class SuperAdminController extends Controller
    {

        private $tenancy;
        private $core;
        private $user;
        private $superAdminSingleton;

        public function __construct()
        {
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);
            $this->core = app(\App\Http\Controllers\CoreController::class);
            $this->user = app(\App\Http\Controllers\UserController::class);
            $this->superAdminSingleton = SuperAdmin::getInstance();
            $this->organisationService = OrganisationService::getInstance();
        }

        public function signin()
        {
            $hostname = $this->user->getHostData();
            return view('super_admin.signin');
            if (empty($hostname)) {

            } else {
                return redirect()->away('https://pasimplify.com');

            }
        }

        function getHostData()
        {
            $this->tenancy->website();
            $hostdata = $this->tenancy->hostname();
            $domain = @explode('.' . config('constants.HOST_SUFFIX'), $hostdata->fqdn)[0];
            //$domain = config('constants.HOST_SUFFIX');
            //session('hostdata', ['subdomain' => $domain]);
            return $this->tenancy->hostname();
        }

        public function doSigninProcess(Request $request)
        {
            $rules = [
                'email'    => 'required|email',
                'password' => 'required',
            ];
            $this->validate($request, $rules);
            $user = SuperadminLogin::where(['email' => $request->email])->first();
            if ($user != NULL && Hash::check($request->password, $user['password'])) {
                session()->put('superadmin', $user);
                return redirect('accounts');
            } else {
                Session::flash('error', "Invalid Credentials , Please try again.");
                return Redirect::back();
            }
        }

        public function settings($id)
        {

            if (session()->has('superadmin')) {
                $dBname = Hostname::with('website')->find($id);//dump($dBname);
                $data['host'] = $dBname;
                $data['dBname'] = $dBname->website->uuid;
                $data['id'] = $id;
                $data['setting'] = AccountSettings::where('account_id', $id)->first();
                $this->tenancy->hostname($dBname);
                $data['acc'] = $this->tenancy->hostname();
                $data['settingData'] = Setting::whereIn('setting_key', ['video_meeting_api_setting'])->get(['setting_key', 'setting_value']);
                $settingData = Setting::where('setting_key', 'adobe_stock_api_setting')->first();
                if ($settingData) {
                    $data['stockData'] = json_decode($settingData->setting_value);
                }
                $data['stock_data'] = AdobePhotosTracking::
                select(
                    'account_id',
                    DB::raw('count(case when type=1 then 1 else null end) as bought_total'),
                    DB::raw('count(case when type=2 then 1 else null end) as used_total'),
                    DB::raw('count(case when type=1 and created_at like \'' . date('Y-m') . '%\' then 1 else null end) as bought_this_month'),
                    DB::raw('count(case when type=2 and created_at like \'' . date('Y-m') . '%\' then 1 else null end) as used_this_month')
                )
                    ->where('account_id', $id)
                    ->groupBy('account_id')
                    ->get();

//            $data['transcribe_data']= TranscribeTracking::
//            select('account_id',
//                DB::raw('sum(case when used_at like \''.date('m-Y').'%\' then time_used else 0 end) as this_month'),
//                DB::raw('sum(time_used) as total_time')
//            )
//                ->where('account_id' , $id)
//                ->groupBy('account_id')
//                ->get();
//
//            $data['transcribe_data']['this_month'] = gmdate("H:i:s", $data['transcribe_data'][0]->this_month);
//            $data['transcribe_data']['total_time'] = gmdate("H:i:s", $data['transcribe_data'][0]->total_time);

                $data['transcribe_data'] = TranscribeTracking::select('account_id', DB::raw('sum(case when used_at like \'' . date('Y-m') . '%\' then time_used else 0 end) as this_month'), DB::raw('sum(time_used) as total_time'))
                    ->where('account_id', $id)
                    ->groupBy('account_id')
                    ->first();
                if ($data['transcribe_data']) {
                    $data['transcribe_data']['this_month'] = gmdate("H:i:s", $data['transcribe_data']->this_month);
                    $data['transcribe_data']['total_time'] = gmdate("H:i:s", $data['transcribe_data']->total_time);
                }
                $settingData = Setting::where('setting_key', 'event_settings')->first();
                if ($settingData) {
                    $data['eventData'] = json_decode($settingData->setting_value);
                }
                $settingData = Setting::where('setting_key', 'adobe_stock_api_setting')->first();
                if ($settingData) {
                    $data['stockData'] = json_decode($settingData->setting_value);
                }
                $settingData = Setting::where('setting_key', 'languages_to_show')->first();
                if ($settingData) {
                    $data['languages'] = json_decode($settingData->setting_value);
                }
                $settingData = Setting::where('setting_key', 'youtube_api_setting')->first();
                if ($settingData) {
                    $data['youtubeData'] = json_decode($settingData->setting_value);
                }
                return view('super_admin.settings', $data);
            } else {
                return redirect('super-admin-login');
            }
        }

        public function adminList()
        {
            if (session()->has('superadmin')) {
                $data['data'] = SuperadminLogin::get();
                return view('super_admin.adminlist', $data);
            } else {
                return redirect('super-admin-login');
            }
        }

        public function newsuperadmin($id = NULL)
        {
            if ($id > 0) {
                $user['user'] = SuperadminLogin::find($id);
                return view('super_admin.newsuperadmin', $user);
            } else {
                return view('super_admin.newsuperadmin');
            }
        }

        public function deltesuperadmin($id)
        {
            if (SuperadminLogin::where('id', $id)->delete())
                return redirect()->back();
        }

        public function savesuperadmin(Request $request)
        {
            if (isset($request->id) && !empty($request->id)) {
                if (strlen($request->password) > 0) {
                    $rules = [
                        'name'                  => 'required',
                        'phone'                 => 'required|numeric|digits:10',
                        'mobile'                => 'required|numeric|digits:10',
                        'password'              => 'min:6|confirmed',
                        'password_confirmation' => 'required|string',
                    ];
                    $update = ['name'           => $request->name,
                               'password'       => Hash::make($request->password),
                               'phone'          => $request->phone,
                               'mobile'         => $request->mobile,
                               'remember_token' => $request->_token];
                } else {

                    $rules = [
                        'name'   => 'required',
                        'phone'  => 'required|numeric|digits:10',
                        'mobile' => 'required|numeric|digits:10',
                    ];
                    $update = ['name'           => $request->name,
                               'phone'          => $request->phone,
                               'mobile'         => $request->mobile,
                               'remember_token' => $request->_token];
                }
                $errors = $this->validate($request, $rules);
                $data = SuperadminLogin::where('id', $request->id)->update([
                    'name'           => $request->name,
                    'password'       => Hash::make($request->password),
                    'phone'          => $request->phone,
                    'mobile'         => $request->mobile,
                    'remember_token' => $request->_token,
                ]);
            } else {
                $rules = [
                    'email'                 => 'required|email|unique:superadmin_logins',
                    'password'              => 'required|string|min:6|confirmed',
                    'password_confirmation' => 'required|string',
                    'name'                  => 'required',
                    'phone'                 => 'required|numeric|digits:10',
                    'mobile'                => 'required|numeric|digits:10',
                ];
                $errors = $this->validate($request, $rules);
                $data = SuperadminLogin::insert([
                    'name'           => $request->name,
                    'email'          => $request->email,
                    'password'       => Hash::make($request->password),
                    'phone'          => $request->phone,
                    'mobile'         => $request->mobile,
                    'remember_token' => $request->_token,
                ]);
            }
            return redirect()->route('adminList');
        }

        public function doSetSettings(Request $request)
        {

            $postData = [
                'test_version'              => checkValSet($request->test_version),
                'light_version'             => checkValSet($request->light_version),
                'mobile_enable'             => checkValSet($request->mobile_enable),
                'email_enabled'             => checkValSet($request->email_enabled),
                'wvm_enable'                => checkValSet($request->wvm_enable),
                'travel_enable'             => checkValSet($request->travel_enable),
                'fvm_enable'                => checkValSet($request->fvm_enable),
                'user_group_enable'         => checkValSet($request->user_group_enable),
                'wiki_enable'               => checkValSet($request->wiki_enable),
                'reminder_enable'           => checkValSet($request->reminder_enable),
                'zip_download'              => checkValSet($request->zip_download),
                'fts_enable'                => checkValSet($request->fts_enable),
                'repd_connect_mode'         => checkValSet($request->repd_connect_mode),
                'prepd_repd_notes'          => checkValSet($request->prepd_repd_notes),
                'project_enable'            => checkValSet($request->project_enable),
                'multiLoginEnabled'         => checkValSet($request->multiLoginEnabled),
                'custom_profile_enable'     => checkValSet($request->custom_profile_enable),
                'meeting_meal_enable'       => checkValSet($request->meeting_meal_enable),
                'notes_to_secretary_enable' => checkValSet($request->notes_to_secretary_enable),
                'import_enable'             => checkValSet($request->import_enable),
                'new_member_enabled'        => checkValSet($request->new_member_enabled),
                'new_member_alert'          => checkValSet($request->new_member_alert),
                'survey_menu_enable'        => checkValSet($request->survey_menu_enable),
                'newsletter_menu_enable'    => checkValSet($request->newsletter_menu_enable),
                'elearning_menu_enabled'    => checkValSet($request->elearning_menu_enabled),
                'crm_menu_enable'           => checkValSet($request->crm_menu_enable),
                'reseau_menu_enable'        => checkValSet($request->reseau_menu_enable),
                'wiki_menu_enable'          => checkValSet($request->wiki_menu_enable),
                'piloter_menu_enable'       => checkValSet($request->piloter_menu_enable),
            ];

            $rec = AccountSettings::where('account_id', $request->account_id)->first();
            if ($rec) {

                //add/update setting as per CRM/NewsLetter
                $this->superAdminSingleton->setAccountSetting($rec, $request, $postData);
                $this->superAdminSingleton->setIcontactAccountSetting($rec, $request, $postData);
                $this->superAdminSingleton->setVideoMeetingAccountSetting($rec, $request, $postData);
                $this->superAdminSingleton->setLanguageSetting($request);
                $this->superAdminSingleton->verticalBarSetting($rec, $request, $postData);
                $this->superAdminSingleton->setResilience($rec, $request, $postData);
                if (($rec->project_enable != $request->project_enable) || (empty($request->project_enable))) {
                    $hostname = Hostname::find($rec->account_id);
                    $host = $this->tenancy->hostname($hostname);
                    $workshops = Workshop::whereIn('code1', ['CRM', 'NSL'])->withoutGlobalScopes()->get(['id']);
                    if (count($workshops) > 0) {
                        Project::where('wid', $workshops->pluck('id'))->withoutGlobalScopes()->update(['display' => checkValSet($request->project_enable)]);
                    }
                }

                if (checkValSet($request->custom_profile_enable) == 0) {
                    if (isset($rec->setting['crm_enable']) && ($rec->setting['crm_enable'] == 1 || $request->crm_enable == 1)) {
                        $postData['custom_profile_enable'] = 1;
                        $postData['crm_menu_enable'] = 1;
                    }
                }
                $postData['date_from'] = date("Y-m-d H:i:s");
                $postData['date_to'] = date("Y-m-d H:i:s", strtotime('+30 days'));
            }
            $accSetting = AccountSettings::where('account_id', $request->account_id)->update($postData);
        $this->superAdminSingleton->setAdditionalData($request);

            return redirect('accounts');
        }


        public function accounts()
        {
            if (session()->has('superadmin')) {
                $data['accounts'] = HostnameModel::with('organisation')->get();
                return view('super_admin.domain_list', $data);
            } else {
                return redirect('super-admin-login');
            }
        }

        public function access($id, Request $request)
        {
            if (session('superadmin')['id']) {
                $host = HostnameModel::find($id);

                $accessToken = md5(base64_encode(date('YmdH:i:s') . time() . $host->id . '' . $host->fqdn . 'ip' . $_SERVER['REMOTE_ADDR']));
                AccountAccessKey::insert(['fqdn_id' => $host->id, 'fqdn_url' => $host->fqdn, 'access_token' => $accessToken, 'ip' => $_SERVER['REMOTE_ADDR']]);
                $url = env('HOST_TYPE') . $host->fqdn . '/app-login/' . $accessToken;

                return view('account_access_process', ['redirect_url' => $url]);
                return redirect()->route('settings', $id);
            } else {
                return redirect('super-admin-login');
            }
        }

        public function logout()
        {
            Auth::logout();
            session()->flush();
            return redirect()->route('signin');
        }

        public function loginApp(Request $request)
        {

            $accessKeyData = DB::connection('mysql')->table('account_access_keys')->where('access_token', $request->token)->first();
            if ($accessKeyData) {
                $email = 'superadmin@opsimplify.com';
                $userData = User::updateOrCreate(['role' => 'M0', 'email' => $email], ['password' => Hash::make($email), 'fname' => 'Super', 'lname' => 'Admin', 'role_commision' => 1, 'role_wiki' => 1]);

                $this->tenancy->website();
                $hostname = $this->tenancy->hostname();
                if ($userData && Auth::loginUsingId($userData->id)) {
                    $rUrl = env('HOST_TYPE') . $hostname->fqdn . '/#/' . 'dashboard';
//                $rUrl = config('constants.REACT_APP_URL') . 'dashboard';
                    return redirect($rUrl);
                }
            } else {
                echo "Access Token Not Authorized !";
            }
        }
        //function for guide-pdf view
        // public function superAdminGuide(){
        //      $data['guide']=DB::connection('mysql')->table('guides')->get();
        //      return view('super_admin.guideList',$data);
        // }
        public function superAdminGuide()
        {
            $data['guide'] = DB::connection('mysql')->table('guides')->get();

            $data['grdp'] = DB::connection('mysql')->table('grdps')->get();

            $data['excludeGuideList'] = DB::connection('mysql')->table('guides')
                ->where('title_en' , config("constants.defaults.s3.notification-allow-guide-EN.name"))
                ->get();
            return view('super_admin.guideList', $data);
        }

        public function uploadGRDP(Request $request)
        {

            $validatedData = $request->validate([
                'file' => 'required',
            ]);
            if ($request->lang == 'project') {
                $validator = Validator::make(
                    [
                        'file'      => $request->file,
                        'extension' => strtolower($request->file->getClientOriginalExtension()),
                    ],
                    [
                        'file'      => 'required',
                        'extension' => 'required|in:csv,xlsx,xls',
                    ]
                );

                if ($validator->fails()) {
                    Session::flash('message', implode(',', $validator->errors()->all()));
                    return redirect()->back();
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422); //validation false return errors
                }

                $grdp = Grdp::find($request->id);
                $filename = strtolower(str_replace(' ', '-', $request->file->getClientOriginalName() . '.' . $request->file->getClientOriginalExtension()));
                $folder = 'GRDP/EN';
                if ($grdp->upload_en != NULL) {

                    Storage::disk('s3')->delete($grdp->upload_en);
                }
                $file_name = $this->core->fileUploadByS3($request->file('file'), $folder, 'public');
                $status = Grdp::where('id', $request->id)->update(['upload_en' => $file_name]);
                if ($status) {
                    Session::flash('message', "Successfully upload");
                } else {
                    Session::flash('message', "Failed to upload");
                }
            } else {
                $grdp = Grdp::find($request->id);

                $filename = strtolower(str_replace(' ', '-', $request->title . '.' . $request->file->getClientOriginalExtension()));
                if ($request->lang == 'en') {
                    $folder = 'GRDP/EN';
                    if ($grdp->upload_en != NULL) {

                        Storage::disk('s3')->delete($grdp->upload_en);
                    }
                    $file_name = $this->core->fileUploadByS3($request->file('file'), $folder, 'public');
                    $status = Grdp::where('id', $request->id)->update(['upload_en' => $file_name]);
                    if ($status) {
                        Session::flash('message', "Successfully upload");
                    } else {
                        Session::flash('message', "Failed to upload");
                    }
                } else {
                    $folder = 'GRDP/FR';
                    $guideDoc = Grdp::find($request->id);
                    if ($grdp->upload_fr != NULL) {

                        Storage::disk('s3')->delete($guideDoc->upload_fr);
                    }
                    $file_name = $this->core->fileUploadByS3($request->file('file'), $folder, 'public');
                    $status = Grdp::where('id', $request->id)->update(['upload_fr' => $file_name]);
                    if ($status) {
                        Session::flash('message', "Successfully upload");
                    } else {
                        Session::flash('message', "Failed to upload");
                    }
                }
            }

            return redirect()->back();
        }

        public function guidePdfUpload(Request $request)
        {
            $validatedData = $request->validate([
                'file' => 'required',
            ]);
            $guideDoc = DB::connection('mysql')->table('guides')->find($request->id);
            $filename = strtolower(str_replace(' ', '-', $request->title . '.' . $request->file->getClientOriginalExtension()));
            if ($request->lang == 'en') {
                $folder = 'guides/EN';
                if ($guideDoc->upload_en != '' || $guideDoc->upload_en != NULL) {
                    Storage::disk('s3')->delete($guideDoc->upload_en);
                }
                $file_name = $this->core->fileUploadByS3($request->file('file'), $folder, 'public');
                $status = DB::connection('mysql')->table('guides')->where('id', $request->id)->update(['upload_en' => $file_name]);
                if ($status) {
                    Session::flash('message', "Successfully upload");
                } else {
                    Session::flash('message', "Failed to upload");
                }
            } else {
                $folder = 'guides/FR';
                $guideDoc = DB::connection('mysql')->table('guides')->find($request->id);
                if ($guideDoc->upload_en != '' || $guideDoc->upload_en != NULL) {
                    Storage::disk('s3')->delete($guideDoc->upload_en);
                }
                $file_name = $this->core->fileUploadByS3($request->file('file'), $folder, 'public');
                $status = DB::connection('mysql')->table('guides')->where('id', $request->id)->update(['upload_fr' => $file_name]);
                if ($status) {
                    Session::flash('message', "Successfully upload");
                } else {
                    Session::flash('message', "Failed to upload");
                }

            }
            return redirect()->back();
        }

        public function downloadGuide(Request $request)
        {

            $rdData = DB::connection('mysql')->table('guides')->find($request->id);
            if ($request->lang == 'EN') {
                $file_name = str_replace(' ', '-', $rdData->title_en);

                $ext = pathinfo($rdData->upload_en, PATHINFO_EXTENSION);

                $download_url = $this->core->getS3Parameter($rdData->upload_en, 1, $file_name . '.' . $ext);
            } else {
                $file_name = str_replace(' ', '-', $rdData->title_fr);
                $ext = pathinfo($rdData->upload_fr, PATHINFO_EXTENSION);
                $download_url = $this->core->getS3Parameter($rdData->upload_fr, 1, $file_name . '.' . $ext);
            }

            if ($download_url != NULL) {
                return redirect($download_url);
            } else {
                $error = 'File doesn`t exist !';
                return view('errors.not_found', ['error' => $error]);
            }
        }

        public function downloadGrdp(Request $request)
        {

            $rdData = Grdp::find($request->id);
            if ($request->lang == 'EN') {
                $file_name = str_replace(' ', '-', ($rdData->file_name_en != NULL) ? $rdData->file_name_en : $rdData->title_en);

                $ext = pathinfo($rdData->upload_en, PATHINFO_EXTENSION);
                if ($rdData->file_name_en != NULL) {
                    $download_url = $this->core->getS3Parameter($rdData->upload_fr, 1, $file_name);
                } else {
                    $download_url = $this->core->getS3Parameter($rdData->upload_en, 1, $file_name . '.' . $ext);
                }
            } else {
                $file_name = str_replace(' ', '-', ($rdData->file_name_fr != NULL) ? $rdData->file_name_fr : $rdData->title_fr);
                $ext = pathinfo($rdData->upload_fr, PATHINFO_EXTENSION);
                if ($rdData->file_name_fr != NULL) {
                    $download_url = $this->core->getS3Parameter($rdData->upload_fr, 1, $file_name);
                } else {
                    $download_url = $this->core->getS3Parameter($rdData->upload_fr, 1, $file_name . '.' . $ext);
                }
            }

            if ($download_url != NULL) {
                return redirect($download_url);
            } else {
                $error = 'File doesn`t exist !';
                return view('errors.not_found', ['error' => $error]);
            }
        }

        public function deleteGuide(Request $request)
        {
            $guideData = DB::connection('mysql')->table('guides')->find($request->id);
            if ($request->lang == 'EN') {
                $d = Storage::disk('s3')->delete($guideData->upload_en);
                if ($d) {
                    $status = DB::connection('mysql')->table('guides')->where('id', $request->id)->update(['upload_en' => '']);
                    Session::flash('message', "Successfully delete");
                } else {
                    $status = 0;
                    Session::flash('message', "Failed to delete");
                }

            } else {
                $d = Storage::disk('s3')->delete($guideData->upload_fr);
                if ($d) {
                    $status = DB::connection('mysql')->table('guides')->where('id', $request->id)->update(['upload_fr' => '']);
                    Session::flash('message', "Successfully delete");
                } else {
                    $status = 0;
                    Session::flash('message', "Failed to delete");
                }
            }
            return redirect()->back();
        }

        public function deleteGrdp(Request $request)
        {
            $guideData = Grdp::find($request->id);
            if ($request->lang == 'EN') {
                $d = Storage::disk('s3')->delete($guideData->upload_en);
                if ($d) {
                    $status = Grdp::where('id', $request->id)->update(['upload_en' => NULL]);
                    Session::flash('message', "Successfully delete");
                } else {
                    $status = 0;
                    Session::flash('message', "Failed to delete");
                }

            } else {
                $d = Storage::disk('s3')->delete($guideData->upload_fr);
                if ($d) {
                    $status = Grdp::where('id', $request->id)->update(['upload_fr' => NULL]);
                    Session::flash('message', "Successfully delete");
                } else {
                    $status = 0;
                    Session::flash('message', "Failed to delete");
                }
            }
            return redirect()->back();
        }

        public function getGuide()
        {
            $res = WorkshopMeta::where(['user_id' => Auth::user()->id, 'role' => 1])->count();
            if (Auth::user()->role == 'M2' && $res) {
                $guides = $guideData = DB::connection('mysql')->table('guides')->where('role', '!=', 0)->get();
            } elseif (Auth::user()->role == 'M2' && $res == 0) {

                $guides = DB::connection('mysql')->table('guides')->where('role', '=', 2)->get();
            } else {
                $guides = $guides = DB::connection('mysql')->table('guides')->get();
            }
            return response()->json($guides);
        }

        public function getReminderStatus()
        {
            $hostname = $this->user->getHostData();
            $fqdn = $hostname->fqdn;
//        $fqdn = 'http://ops1.opsimplify.com/';
            $data = DB::connection('system')->table('weekly_reminders')->where(['fqdn' => $hostname->fqdn])->first();
            if (config('constants.QUALIFICATION')) {
                $data->qualification_reminders = QualificationReminder::all(['id', 'reminder_time', 'section_id', 'week_reminder']);
            } elseif (empty($data)) {
                $data['qualification_reminders'] = [];
            }
            return response()->json($data);

        }


        public function updateReminderStatus(Request $request)
        {
            $hostname = $this->user->getHostData();
            $fqdn = $hostname->fqdn;
            // $fqdn='http://ops1.opsimplify.com/';
            //var_dump($request->all());exit;
            if (isset($request->weekday)) {
                $data = ['weekday' => $request->weekday];
            } elseif (isset($request->time_frame) && !empty($request->time_frame)) {
                $data = ['time_frame' => $request->time_frame];
            } else {
                if ($request->on_off == 'undefined')
                    $onOff = 1;
                else
                    $onOff = isset($request->on_off) ? $request->on_off : '';
                if (!empty($on_off)) {
                    $request['on_off'] = $onOff;
                }
                $data = $request->all();
            }

//var_dump($hostname->fqdn);exit;
            $res = DB::connection('mysql')->table('weekly_reminders')->where('fqdn', $hostname->fqdn)->update($data);
            $reminder = DB::connection('mysql')->table('weekly_reminders')->where(['fqdn' => $hostname->fqdn])->first(['on_off', 'time_frame', 'weekday']);
            if ($res) return response()->json([
                'status'   => 1,
                'msg'      => "Update Successfully.",
                'response' => $reminder,
            ]); else
                return response()->json(['status' => 0, 'msg' => "Updation Failed."]);
        }

        public function updateAdminStatus(Request $request)
        {

            try {

                $validator = Validator::make($request->all(), [
                    'type'  => 'required',
                    'value' => 'required',
                    'id'    => 'required',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);//validation false return errors
                }
                $data = DB::connection('mysql')->table('account_settings')->where('id', $request->id)->update([$request->type => $request->value]);
                if ($data) {
                    return response()->json(['status' => TRUE, 'data' => DB::connection('mysql')->table('account_settings')->where('id', $request->id)->first()], 200);
                } else {
                    return response()->json(['status' => FALSE, 'data' => 0], 200);
                }
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        public function uploadProjectTemplate(Request $request)
        {
            $wid = Workshop::where('code1', 'NSL')->first(['id']);

            if ($request->hasFile('file')) {
                $filename = strtolower(str_replace(' ', '-', $request->title . '.' . $request->file->getClientOriginalExtension()));
                $folder = 'GRDP/EN';
                $file_name = $this->core->fileUploadByS3($request->file('file'), $folder, 'public');
                $status = Grdp::where('id', $request->id)->update(['upload_en' => $file_name]);
                if ($status) {
                    Session::flash('message', "Successfully upload");
                } else {
                    Session::flash('message', "Failed to upload");
                }

                //Store $filenametostore in the database
                $file = Newsletter::get()->first();
                if ($wid && empty($file)) {
                    $data = ['workshop_id' => $wid, 'file_name' => $filenamewithextension, 'created_at' => Carbon::now('Europe/Paris')->format('Y-m-d H:i:s')];
                    $user_id = Newsletter::insertGetId($data);
                } else if (($wid) && (!empty($file))) {
                    $data = ['workshop_id' => $wid, 'file_name' => $filenamewithextension, 'created_at' => Carbon::now('Europe/Paris')->format('Y-m-d H:i:s')];
                    $user_id = Newsletter::where('workshop_id', $wid)->update($data);
                }

                //Upload File to s3 //todo: uncomment below line
                //Storage::disk('s3')->put($filenametostore, fopen($request->file('file'), 'r+'), 'public');

                //return a URL of your uploaded file
                $url = Storage::disk('s3')->url('file');
            }

            return redirect()->back();
        }

        public function uploadTemplate($id)
        {
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|exists:hostnames,id',
            ]);
            if ($validator->fails()) {
                Session::flash('error', "Invalid Data , Please try again.");
                return Redirect::back();
            }
            $hostnames = Hostname::whereId($id)->first();
            $this->tenancy->hostname($hostnames);
            $data['acc'] = $this->tenancy->hostname();
            $data['data'] = QualificationTemplate::all();
            return view('super_admin.templatelist', $data);

        }

        public function addTemplate($id)
        {
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|exists:hostnames,id',
            ]);
            if ($validator->fails()) {
                Session::flash('error', "Invalid Data , Please try again.");
                return Redirect::back();
            }
            $data['id'] = $id;
            $data['lang'] = ['EN' => 'English', 'FR' => 'Franch'];
            $hostnames = Hostname::whereId($id)->first();
            $this->tenancy->hostname($hostnames);
            $data['acc'] = $this->tenancy->hostname();
            return view('super_admin.addTemplate', $data);

        }

        public function saveTemplate(Request $request, $id)
        {
            $validator = Validator::make(['id' => $id, 'name' => $request->name, 'language' => $request->language], [
                'id'       => 'required|exists:hostnames,id',
                'name'     => 'required',
                'language' => 'required',
            ]);
            if ($validator->fails()) {
                Session::flash('error', "Invalid Data , Please try again.");
                return Redirect::back();
            }
            $hostnames = Hostname::whereId($id)->first();
            $this->tenancy->hostname($hostnames);
            $qual = QualificationTemplate::create(['title' => $request->name, 'language' => $request->language]);
            return redirect()->route('upload-template-setting', $id);
        }

        public function editTemplate($id, $accId)
        {
            try {

                $validator = Validator::make(['acc_id' => $accId, 'id' => $id], [
                    'id'     => 'required',//|exists:qualification_templates,id
                    'acc_id' => 'required|exists:hostnames,id',
                ]);
                if ($validator->fails()) {
                    Session::flash('error', "Invalid Data , Please try again.");
                    return Redirect::back();
                }
                $data['acc_id'] = $accId;
                $data['id'] = $id;
                $data['lang'] = ['EN' => 'English', 'FR' => 'Franch'];
                $hostnames = Hostname::whereId($accId)->first();
                $this->tenancy->hostname($hostnames);
                $data['acc'] = $this->tenancy->hostname();
                $data['data'] = QualificationTemplate::find($id);
                return view('super_admin.editTemplate', $data);
            } catch (\Exception $e) {
                Session::flash('error', $e->getMessage());
                return Redirect::back();
            }


        }

        public function updateTemplate(Request $request, $id, $acc)
        {

            $validator = Validator::make(['id' => $id, 'name' => $request->name, 'language' => $request->language, 'acc_id' => $acc], [
                'id'       => 'required',//|exists:tenant.qualification_templates,id
                'acc_id'   => 'required|exists:hostnames,id',
                'name'     => 'required',
                'language' => 'required',
            ]);

            if ($validator->fails()) {
                Session::flash('error', "Invalid Data , Please try again.");
                return Redirect::back();
            }
            $hostnames = Hostname::whereId($acc)->first();
            $this->tenancy->hostname($hostnames);
            $qual = QualificationTemplate::where('id', $id)->update(['title' => $request->name, 'language' => $request->language]);
            return redirect()->route('upload-template-setting', $acc);
        }

        public function deleteTemplate($id, $acc)
        {
            $hostnames = Hostname::whereId($acc)->first();
            $this->tenancy->hostname($hostnames);
            if (QualificationTemplate::where('id', $id)->delete())
                return redirect()->route('upload-template-setting', $acc);
        }

        public function uploadProject(Request $request)
        {
            // dd($request->all());
            $validatedData = $request->validate([
                'file' => 'required',
            ]);
            if ($request->lang == 'EN') {
                $validator = Validator::make(
                    [
                        'file'      => $request->file,
                        'extension' => strtolower($request->file->getClientOriginalExtension()),
                    ],
                    [
                        'file'      => 'required',
                        'extension' => 'required|in:csv,xlsx,xls',
                    ]
                );

                if ($validator->fails()) {
                    Session::flash('message', implode(',', $validator->errors()->all()));
                    return redirect()->back();
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422); //validation false return errors
                }

                $grdp = Grdp::find($request->id);
                // dd($grdp);
                $file = $request->file->getClientOriginalName();
                $fileName = pathinfo($file, PATHINFO_FILENAME);
                $filename = strtolower(str_replace(' ', '-', $fileName . '.' . $request->file->getClientOriginalExtension()));
                $folder = 'GRDP/EN';
                if ($grdp != NULL && $grdp->upload_en != NULL) {
                    Storage::disk('s3')->delete($grdp->upload_en);
                }

                $file_name = $this->core->fileUploadByS3($request->file('file'), $folder, 'public');
                if ($request->id != 0) {
                    $status = Grdp::where('id', $request->id)->update(['upload_en' => $file_name, 'file_name_en' => $filename]);
                } else {
                    $status = Grdp::insert(['title_en' => 'Project Template Upload EN', 'title_fr' => 'Project Template Upload FR', 'upload_en' => $file_name, 'type' => 1, 'file_name_en' => $filename]);
                }
                if ($status) {
                    Session::flash('message', "Successfully upload");
                } else {
                    Session::flash('message', "Failed to upload");
                }
            } else {
                $validator = Validator::make(
                    [
                        'file'      => $request->file,
                        'extension' => strtolower($request->file->getClientOriginalExtension()),
                    ],
                    [
                        'file'      => 'required',
                        'extension' => 'required|in:csv,xlsx,xls',
                    ]
                );

                if ($validator->fails()) {
                    Session::flash('message', implode(',', $validator->errors()->all()));
                    return redirect()->back();
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422); //validation false return errors
                }

                $grdp = Grdp::find($request->id);
                $file = $request->file->getClientOriginalName();
                $fileName = pathinfo($file, PATHINFO_FILENAME);
                $filename = strtolower(str_replace(' ', '-', $fileName . '.' . $request->file->getClientOriginalExtension()));

                $folder = 'GRDP/FR';
                if ($grdp != NULL && $grdp->upload_fr != NULL) {

                    Storage::disk('s3')->delete($grdp->upload_fr);
                }
                $file_name = $this->core->fileUploadByS3($request->file('file'), $folder, 'public');
                if ($request->id != 0) {
                    $status = Grdp::where('id', $request->id)->update(['upload_fr' => $file_name, 'file_name_fr' => $filename]);
                } else {
                    $status = Grdp::insert(['title_en' => 'Project Template Upload EN', 'title_fr' => 'Project Template Upload FR', 'upload_fr' => $file_name, 'type' => 1, 'file_name_fr' => $filename]);
                }
                if ($status) {
                    Session::flash('message', "Successfully upload");
                } else {
                    Session::flash('message', "Failed to upload");
                }
            }
            return redirect()->back();
        }

        public function adobeStockTracking()
        {
            if (session()->has('superadmin')) {
                $data = AdobePhotosTracking::with('hostname')
                    ->select(
                        'account_id',
                        DB::raw('count(case when type=1 then 1 else null end) as bought_total'),
                        DB::raw('count(case when type=2 then 1 else null end) as used_total'),
                        DB::raw('count(case when type=1 and created_at like \'' . date('Y-m') . '%\' then 1 else null end) as bought_this_month'),
                        DB::raw('count(case when type=2 and created_at like \'' . date('Y-m') . '%\' then 1 else null end) as used_this_month')
                    )
                    ->groupBy('account_id')
                    ->get();

                return view('super_admin.adobe_stock_setting')->with('data', $data);
            } else {
                return redirect('super-admin-login');
            }
        }

        public function transcribeTracking($date = '')
        {
            if (session()->has('superadmin')) {
                $reg_ex = '/^\d{4}-\d{2}.*$/'; // to match YYYY-MM* format
                $month = date('Y-m');
                if (preg_match($reg_ex, $date)) {
                    $month = substr($date, 0, 7);
                }
                $data = TranscribeTracking::
                with('hostname')
                    ->whereHas('hostname')
                    ->select('account_id', DB::raw('sum(case when type=2 then time_used else 0 end) as assistance_time'), DB::raw('sum(case when type=1 then time_used else 0 end) as noted_time'), DB::raw('sum(case when used_at like \'' . $month . '%\' then time_used else 0 end) as this_month'), DB::raw('sum(time_used) as total_time'))
                    ->where('used_at', 'like', $date . '%')
                    ->groupBy('account_id')
                    ->get();
                //            dd($data[0]->hostname->fqdn);
                return view('super_admin.transcribe-tracking')->with('data', $data);
            } else {
                return redirect('super-admin-login');
            }
        }

    /**
     * To sync the bluejeans users with the current account table
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
        public function syncBlueJeansUser(Request $request) {
            try {
                $hostname = Hostname::find($request->id);
                $host = $this->tenancy->hostname($hostname);
                if (session()->has('superadmin')) {
                    if (!$this->superAdminSingleton->isBjDetailsAvailable()) {
                        return redirect()->back()->withErrors(['bjmSyncError' => 'Please Enter And Save BJ Credentials before Sync BJ Users']);
                    }
                    MeetingService::getInstance()->syncBlueJeansUser();
                    return redirect()->back();
                } else {
                    return redirect('super-admin-login');
                }
            } catch (CustomException $e) {
                return redirect()->back()->withErrors(['bjmSyncError' => $e->getMessage()]);
            }
        }

        public function storeSuperAdminHostname(Request $request){
            try{
                $data = OrganisationService::getInstance()->addSuperAdminHostname($request);
            return response()->json(['status' => true, 'data' => $data],201);
            }catch (\Exception $e){
                return response()->json(['status' => false, 'msg' => $e->getMessage()],500);
            }
        }

        public function viewBulkCreation(Request $request) {
//            if (session()->has('superadmin')) {
//                $data = BulkAccAdmin::with('accountCreatedToday')
//                    ->whereDate('created_at', '=', Carbon::today()->toDateString())
//                    ->where('super_admin_id', Auth::user()->id)->get();
//            }
            $data = collect();
            return view('super_admin.bulkAccCreation')->with('data', $data);
        }

        public function storeOrgAcc(BulkAccCreateRequest $request) {
            try {
//                $commandOptions = [
//                    '--fname'   => $request->input('orgFname'),
//                    '--lname'   => $request->input('orgLname'),
//                    '--email'   => $request->input('orgEmail'),
//                    '--name'    => $request->input('orgName'),
//                    '--accName' => $request->input('accName'),
//                ];
                $hostname = OrganisationService::getInstance()->createAccount(
                    $request->input('orgFname'),
                    $request->input('orgLname'),
                    $request->input('orgEmail'),
                    $request->input('orgName'),
                    $request->input('accName')
                );
//                $result = Artisan::call('account:create', $commandOptions);
                return response()->json([
                    'status' => true,
                    'fqdn' => $request->input('accName') . '.' . env('HOST_SUFFIX'),
                    'url' => env('HOST_TYPE') . env('HOST_SUFFIX') . "/bulk-acc/super-staff-access/{$hostname->id}",
                ]);
            } catch (CustomValidationException $e) {
                return $e->render();
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'msg' => 'Error in account create', 'message' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
            }
        }
    
        /**
         * -------------------------------------------------------------------------------------------------------------
         * @description This method will prepare the access token and user email
         * that access token and email will transferred to subdomain redirect url
         * so the subdomain redirect url can validate via access token and can use the email to login as user
         * -------------------------------------------------------------------------------------------------------------
         *
         * @param Request $request
         * @param $id
         * @return JsonResponse|RedirectResponse|View
         */
        public function staffLoginAccess(Request $request, $id) {
            try {
                if (session()->has('superadmin') && $host = Hostname::find($id)) {
                    $accessToken = md5(base64_encode(date('YmdH:i:s') . time() . $host->id . '' . $host->fqdn . 'ip' . $_SERVER['REMOTE_ADDR']));
                    AccountAccessKey::insert(['fqdn_id' => $host->id, 'fqdn_url' => $host->fqdn, 'access_token' => $accessToken, 'ip' => $_SERVER['REMOTE_ADDR']]);
                    $email = session()->get('superadmin')->email;
                    $url = env('HOST_TYPE') . "{$host->fqdn}/bulk-acc/redirect-access/$email/$accessToken";
                    return view('account_access_process', ['redirect_url' => $url]);
                }
                return redirect('super-admin-login');
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'error'   => $e->getTrace(),
                ]);
            }
        }
    
        /**
         * -------------------------------------------------------------------------------------------------------------
         * @description This method will be called on sub domain and check the access token passed is valid or not
         * if valid find the given user and login with that user.
         * -------------------------------------------------------------------------------------------------------------
         *
         * @param $email
         * @param $token
         * @return RedirectResponse|JsonResponse
         */
        public function bulkAccRedirectApp($email, $token) {
            try {
                $url = OrganisationService::getInstance()->prepareStaffAccessLink($token, $email);
                return redirect($url);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'error' => $e->getTrace(),
                ]);
            }
        }
    }
