<?php

    namespace App\Http\Controllers;

    use App\AccountSettings;
    use App\Color;
    use App\Milestone;
    use App\Model\LabelCustomization;
    use App\Organisation;
    use App\Project;
    use App\Role;
    use App\Setting;
    use App\Signup;
    use App\User;
    use Auth;
    use DB;
    use Hash;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\App;
    use Modules\Cocktail\Services\V2Services\KctCoreService;

    class InitController extends Controller
    {
        private $core, $tenancy;

        public function __construct()
        {
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);
            $this->core = app(\App\Http\Controllers\CoreController::class);
        }

        function checkLogin(Request $request)
        {
            $user = $this->core->checkUserExist($request->email);
            if (count($user) > 0) {
                if (password_verify($request->password, $user->password)) {
                    $role = $this->core->roleData($user->role);
                    $data['auth'] = $user;
                    $data['role'] = [];
                    $data['status'] = 1;
                    foreach ($role as $key => $value) {
                        $data['role'][$value['action_react']] = $value[$user->role];
                    }
                    return response()->json(['status' => 1, $data]);
                } else
                    return response()->json(['status' => 0, 'msg' => 'Invalid Password !']);
            } else {
                return response()->json(['status' => 0, 'msg' => 'Invalid Email !']);
            }
        }

        function forgetPassword(Request $request)
        {
            $user = User::where('email', 'amit@sharabh.com')->first();
            if (count($user) > 0) {
                $key = $this->generateRandomString(36);
                $user->identifier = $key;
                $user->save();
                $mailData['mail'] = ['subject' => 'Verify Email', 'email' => $request->email, 'firstname' => 'OP Simplify', 'msg' => 'Your Verification Code for OPsimplify Forget Password is http://localhost:3000/#/reset-password/' . $key];
                if ($this->core->SendEmail($mailData)) {
                    return response()->json(['status' => 1, 'msg' => 'Verify Account via link we place in your mail inbox.']);
                } else {
                    return response()->json(['status' => 0, 'msg' => 'Somthing Goes Worng.']);
                }
            } else {
                return response()->json(['status' => 0, 'msg' => 'Incorrect Email!']);
            }
        }

        private function generateRandomString($length = 10)
        {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }

        function resetPassword(Request $request)
        {
            if (User::where('identifier', $request->identifier)->update(['password' => Hash::make($request->password), 'identifier' => NULL])) {
                return response()->json(['status' => 1, 'msg' => 'Password Changed ! Login to Continue.']);
            } else {
                return response()->json(['status' => 0, 'msg' => '404 Not Found / Link Expired!']);
            }
        }

        function send_email(Request $request)
        {

            $userExist = User::where('email', $request->email)->get();
            if (count($userExist) == 0) {
                $code = genRandomNum(6);
                $mailData['mail'] = ['subject' => 'Verify Email', 'email' => $request->email, 'firstname' => 'Amit', 'msg' => 'Your Verification Code for OPsimplify SignUp is ' . $code];
                if ($this->core->SendEmail($mailData)) {
                    $lastId = Signup::insertGetId(['email' => $request->email, 'code' => $code]);
                    return response()->json(['id' => $lastId, 'status' => 1]);
                } else {
                    return response()->json(['id' => '', 'status' => 2]);
                }
            } else {
                return response()->json(['id' => '', 'status' => 3]);
            }
        }

        function check_otp(Request $request)
        {
            $res = Signup::where('id', $request->id)->where('email', $request->email)->where('code', $request->otp)->first();
            if ($res) {
                $lastId = Organisation::insertGetId(['email' => $res->email, 'password' => Hash::make($res->email)]);
                //Signup::where('email', $res->email)->delete();
                //return response()->json(['id'=>$lastId]);
            }
            return response()->json(['id' => 0]);
        }

        function UserUpdate(Request $request)
        {
            if (Organisation::where('id', $request->id)->update(['fname' => $request->u_fname, 'lname' => $request->u_lname, 'password' => Hash::make($request->u_pass)])) {
                return response()->json(['id' => $request->id]);
            } else {
                return response()->json(['id' => 0, 'msg' => 'Somthing Worng Happened.']);
            }
        }

        function orgSave(Request $request)
        {
            $dataArray['members_count'] = $request->members;
            $dataArray['acronym'] = $request->acronym;
            $dataArray['sector'] = $request->sector;
            $dataArray['permanent_member'] = $request->pMember;
            $dataArray['name_org'] = $request->name;
            $res = Organisation::where('id', $request->id)->update($dataArray);
            if ($res) {
                return response()->json(['done' => 1]);
            } else {
                return response()->json(['done' => 0]);
            }
        }

        function initData(Request $request)
        {
            $this->tenancy->website();
            $hostname = $this->tenancy->hostname();
            $data['guest'] = [];
            $data['auth'] = Auth::user();
            $settingData = Setting::where('setting_key', 'languages_to_show')->first();
            if ($settingData) {
                $data['auth']['enabled_languages'] = json_decode($settingData->setting_value);
            }
            if (Auth::user()->role == 'M3' && session()->has('guest')) {
                $data['guest'] = session()->get('guest');
            }
            $role = $this->core->roleData(Auth::user()->role);
            foreach ($role as $key => $value) {
                $data['role'][$value['action_react']] = $value[Auth::user()->role];
            }
            $res = DB::connection('tenant')->table('role_permissions')->get();
            foreach ($res as $val) {
                $wRole['W0'][$val->action_react] = $val->W0;
                $wRole['W1'][$val->action_react] = $val->W1;
                $wRole['W2'][$val->action_react] = $val->W2;
            }
            $data['workshop_roles'] = $wRole;
            $data['document_type'] = $this->core->getDocTypeByLogin();
            $data['issuer'] = $this->core->getIssuerByLogin();
            $data['workshop'] = $this->core->getWorkshopByLogin();
            if (count($data['workshop']) > 0) {
                $data['workshop'] = collect($data['workshop'])->unique('id')->toArray();
                $data['workshop'] = array_values($data['workshop']);
            }

            $data['org_icon'] = getSettingData('platform_graphic');
            $data['graphic_setting'] = getSettingData('graphic_config', 1);
            $data['pdf_setting'] = getSettingData('pdf_graphic', 0);
            $data['email_setting'] = getSettingData('email_graphic', 0);

            $data['org_data'] = Organisation::select('fname', 'lname', 'email', 'acronym')->first();

            $data['org_data']['account_name'] = $hostname->fqdn;
            $data['grdp'] = DB::connection('mysql')->table('grdps')->where('type', 0)->get();
            $data['guides'] = [
                'bneg' => DB::connection('mysql')->table('guides')
                    ->where('title_en', config('constants.defaults.s3.notification-allow-guide-EN.name'))
                    ->first()
            ];
            $data['overdue_alert'] = Setting::where('setting_key', 'overdue_alert')->first();
            $data['getColor'] = Color::all();
            $data['customizedLabels'] = LabelCustomization::all();
            $data['staff'] = session()->has('data') ? session()->get('data') : 0;
            //Get serverside date
            $data['date'] = date('Y-m-d h:i:s');
            $acc_id = $hostname->id;

            //$acc_id = 1;

            $data['super_permission'] = DB::connection('mysql')->table('account_settings')->where('account_id', $acc_id)->orderBy('id', 'desc')->first();
            $data['super_permission']->qualification_domain_allowed = (in_array($acc_id, [1, 38]) ? 1 : 0);
            $data['super_permission']->event_setting = $this->getEventData($acc_id, $data['super_permission']);
            $data['super_permission']->qualification_qualifelec_allowed = (in_array($acc_id, [2]) ? 1 : 0);
            $data['super_permission']->vertical_bar_enable = $this->getVerticalBarData($acc_id, $data['super_permission']);
            $data['super_permission']->consultation_setting = $this->getConsultationData($acc_id, $data['super_permission']);

            $data['super_permission']->meeting_creation = $this->getVideoData();

            echo "window.auth = " . json_encode($data);
        }

        public function getEventData($accountId, $accountSetting)
        {
            $account_setting = $accountSetting->setting ? json_decode($accountSetting->setting) : NULL;
            
            $event_enabled =($account_setting && isset($account_setting->event_enabled))? $account_setting->event_enabled : 0;
            $keep_contact_enable = $event_enabled && isset($account_setting->event_settings->keep_contact_enable) && $account_setting->event_settings->keep_contact_enable ? 1 : 0;
            $bluejeans_enable = $event_enabled && isset($account_setting->event_settings->bluejeans_enabled) && $account_setting->event_settings->bluejeans_enabled ? 1 : 0;
            
            $setting = Setting::where('setting_key', 'event_settings')->first();
            $s = $event_enabled && $setting ? json_decode($setting->setting_value, 1) : NULL;
            $conferenceType =
                $s
                && isset($s['event_current_conference'])
                && isset($account_setting->event_settings->event_conference_enabled)
                && $account_setting->event_settings->event_conference_enabled
                    ? $s['event_current_conference'] :
                    NULL;
            $event_org_setting = $s && isset($s['event_org_setting']) ? $s['event_org_setting'] : NULL;
            $virtual_event_org_setting = $s && isset($s['event_virtual_org_setting']) ? $s['event_virtual_org_setting'] : NULL;

            $iEvt = $this->getEventInternalSetting($s);
            $vEvt = $this->getEventVirtualSetting($s);
    
            // to identify to show or hide the event commission tab
            $showCommissionTab = $this->canShowEventCommissionTab();
    
            // to identify to show or hide the event organiser tab
            $showOrganiserTab = $this->canShowEventOrganiserTab($iEvt['default_organiser_id'], $vEvt['virtual_event_do_id']);
            
            $result = [
                'event_enabled'           => $event_enabled ? 1 : 0,
                'keep_contact_enable'     => $keep_contact_enable,
                'event_current_conference' => $conferenceType,
                'bluejeans_enabled'       => $bluejeans_enable,
                'visible' => [
                    'commission_tab' => $showCommissionTab,
                    'organizer_tab' => $showOrganiserTab,
                ]
            ];
            return array_merge($result, $iEvt, $vEvt);
        }
    
        /**
         * -------------------------------------------------------------------------------------------------------------
         * @description to get the internal event related settings
         * -------------------------------------------------------------------------------------------------------------
         *
         * @param $s
         * @return array
         */
        public function getEventInternalSetting($s) {
            $event_org_setting = $s && isset($s['event_org_setting']) ? $s['event_org_setting'] : NULL;
            $organiser = $event_org_setting && isset($event_org_setting['default_organiser']) ? User::find($event_org_setting['default_organiser']) : NULL;
            $result['default_organiser_id'] = $organiser ? $organiser->id : NULL;
            $result['default_organiser_name'] = $organiser ? $organiser->fname . ' ' . $organiser->lname : NULL;
            $result['default_organiser_email'] = $organiser ? $organiser->email : NULL;
            $result['prefix'] = $event_org_setting && isset($event_org_setting['prefix']) ? $event_org_setting['prefix'] : NULL;
            return $result;
        }
    
        /**
         * -------------------------------------------------------------------------------------------------------------
         * @description to get the virtual event setting data for init
         * -------------------------------------------------------------------------------------------------------------
         *
         * @param $s
         * @return array
         */
        public function getEventVirtualSetting($s) {
            $virtual_event_org_setting = $s && isset($s['event_virtual_org_setting'])
                ? $s['event_virtual_org_setting']
                : NULL;
            $organiser = $virtual_event_org_setting && isset($virtual_event_org_setting['default_organiser'])
                ? User::find($virtual_event_org_setting['default_organiser'])
                : NULL;
            $result['virtual_event_do_id'] = $organiser ? $organiser->id : NULL;
            $result['virtual_event_do_email'] = $organiser ? $organiser->email : NULL;
            $result['virtual_event_do_name'] = $organiser
                ? $organiser->fname . ' ' . $organiser->lname
                : NULL;
            $result['virtual_event_prefix'] = $virtual_event_org_setting && isset($virtual_event_org_setting['prefix'])
                ? $virtual_event_org_setting['prefix']
                : NULL;
            $result['kct_graphics_logo'] = isset($s['event_kct_setting']['kct_graphics_logo'])
                ? app()->make(CoreController::class)->getS3Parameter($s['event_kct_setting']['kct_graphics_logo'])
                : null;
            $result['kct_graphics_colo1'] = isset($s['event_kct_setting']['kct_graphics_color1'])
                ? $s['event_kct_setting']['kct_graphics_color1']
                : null;
            $result['kct_graphics_colo2'] = isset($s['event_kct_setting']['kct_graphics_color2'])
                ? $s['event_kct_setting']['kct_graphics_color2']
                : null;
            $result['kct_graphics_logo_is_default'] = KctCoreService::getInstance()->isLogoDefault(
                isset($s['event_kct_setting']['kct_graphics_logo'])? $s['event_kct_setting']['kct_graphics_logo'] : ''
            ) ? 1 : 0;
            return $result;
        }
    
        /**
         * -------------------------------------------------------------------------------------------------------------
         * @description to find the current user have ability to see the event commission tab or not
         * -------------------------------------------------------------------------------------------------------------
         *
         * @return int
         */
        public function canShowEventCommissionTab() {
            return Auth::check()
            && (
                // user have role commission 1, .i.e. user have ability to become a secretory in workshop
                Auth::user()->role_commision
                    // or user have role of higher user like org or super
                || in_array(Auth::user()->role, ['M0', 'M1'])
            )
                ? 1
                : 0;
        }
    
        /**
         * -------------------------------------------------------------------------------------------------------------
         * @description to find the current user have ability to see the event organiser tab or not
         * -------------------------------------------------------------------------------------------------------------
         *
         * @param mixed ...$organisers // the organisers
         * @return int
         */
        public function canShowEventOrganiserTab(...$organisers) {
            return Auth::check() // user must logged in
            && (
                // either user is super admin or org admin then show organiser tab
                in_array(Auth::user()->role, ['M0', 'M1'])
                // or user as default organiser set for the virtual or internal events, then show organiser tab
                || in_array(Auth::user()->role, $organisers)
            )
                ? 1
                : 0;
        }

        public function languageChange(Request $request)
        {
            session()->put('lang', $request->lang);
            if (isset(Auth::user()->id)) {
                if (isset($request->lang) && !empty($request->lang)) {
                    $user = User::where('id', Auth::user()->id)->update(['setting' => '{"lang":"' . $request->lang . '"}']);
                    $lang = json_decode(User::find(Auth::user()->id)->setting);
                    //return response($lang->lang);
                } else {
                    $lang = json_decode($request->user()->setting);
                    //return response($lang->lang);
                }
            }
            return response(session()->get('lang'));
        }

        public function getRoleList()
        {
            return response()->json(Role::all());
        }

        public function createProject(Request $request)
        {
            $flag = 0;
            $request['user_id'] = Auth::user()->id;
            $pid = Project::insertGetId($request->all());
            if ($pid != NULL && $pid > 0) {
                $milestone = Milestone::insertGetId(['project_id' => $pid, 'label' => 'end-of-project', 'end_date' => date('Y/m/d', strtotime('12/31')), 'user_id' => Auth::user()->id, 'start_date' => date('Y-m-d')]);
                if ($milestone > 0) {
                    $flag = $pid;
                }
            }
            return response($flag);
        }

        public function createMilestone(Request $request)
        {
            $request['user_id'] = Auth::user()->id;
            if (empty($request->end_date)) {
                $request['end_date'] = date('Y-m-d');
            } else {
                $request['end_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $request->end_date)));
            }
            $request['start_date'] = date('Y-m-d');
            $pid = Milestone::insertGetId($request->except('update_project_end_date'));
            if ($pid) {
                $project = Project::find($request->project_id);
                if (isset($request->update_project_end_date) && $request->update_project_end_date) {
                    $project->update(['end_date' => $request['end_date']]);
                }
                $data = ['project_label' => $project->project_label, 'label' => $request->label, 'id' => $pid];
                return response()->json($data);
            }
            return response()->json(0);
        }

        public function fetchMilestone($q, $wid)
        {
            $result = Project::with('milestone')->where('project_label', 'like', '%' . $q . '%')->where('wid', $wid)->get();
            return response()->json($result);
        }

        public function languageChangeWeb(Request $request)
        {
            if (isset($request->lang) && !empty($request->lang)) {
                $user = User::where('id', Auth::user()->id)->update(['setting' => '{"lang":"' . $request->lang . '"}']);
                $lang = json_decode(User::find(Auth::user()->id)->setting);
                return response($lang->lang);
            } else {
                $lang = json_decode($request->user()->setting);
                return response($lang->lang);
            }

        }

        public function getVerticalBarData($accountId, $accountSetting)
        {

            $verticalBarEnable = 0;
            if ((isset($accountSetting) && isset(json_decode($accountSetting->setting)->vertical_bar_enable) && json_decode($accountSetting->setting)->vertical_bar_enable)) {
                $verticalBarEnable = json_decode($accountSetting->setting)->vertical_bar_enable;
                $temp = ($accountSetting->setting && is_string($accountSetting->setting) ? json_decode($accountSetting->setting, 1) : NULL);
                return [
                    'vertical_bar_enable'    => isset($temp['vertical_bar_enable']) ? $temp['vertical_bar_enable'] : 0,
                    'add_module_enable'      => isset($temp['add_module_enable']) ? $temp['add_module_enable'] : 0,
                    'messenger_enable'       => isset($temp['vertical_messenger_enable']) ? $temp['vertical_messenger_enable'] : 0,
                    'feature_request_enable' => isset($temp['feature_request_enable']) ? $temp['feature_request_enable'] : 0,
                    'help_enable'            => isset($temp['help_enable']) ? $temp['help_enable'] : 0,
                    'share_enable'           => isset($temp['share_enable']) ? $temp['share_enable'] : 0,
                    'others_enable'          => isset($temp['others_enable']) ? $temp['others_enable'] : 0,
                    'news_letter_enable'     => isset($temp['vertical_news_letter_enable']) ? $temp['vertical_news_letter_enable'] : 0,
                    'direct_video_enable'    => isset($temp['direct_video_enable']) ? $temp['direct_video_enable'] : 0,
                    'event_enabled'          => isset($temp['vertical_event_enabled']) ? $temp['vertical_event_enabled'] : 0,
                ];

            } else {
                return [
                    'vertical_bar_enable'    => 0,
                    'add_module_enable'      => 0,
                    'messenger_enable'       => isset($accountSetting->setting['vertical_messenger_enable']) ? $accountSetting->setting['vertical_messenger_enable'] : 0,
                    'feature_request_enable' => 0,
                    'help_enable'            => 0,
                    'share_enable'           => 0,
                    'others_enable'          => 0,
                    'news_letter_enable'     => isset($accountSetting->setting['vertical_news_letter_enable']) ? $accountSetting->setting['vertical_news_letter_enable'] : 0,
                    'direct_video_enable'    => isset($accountSetting->setting['direct_video_enable']) ? $accountSetting->setting['direct_video_enable'] : 0,
                    'event_enabled'          => isset($accountSetting->setting['vertical_event_enabled']) ? $accountSetting->setting['vertical_event_enabled'] : 0,
                ];
            }

        }

        public function getConsultationData($accountId, $accountSetting)
        {

            $consultationEnable = 0;
            $reinventEnable = 0;

            $setting = json_decode($accountSetting->setting);
            if ((isset($accountSetting) && isset($setting->consultation_enable) && isset($setting->reinvent_enable))) {
                $consultationEnable = isset($setting->consultation_enable) ? $setting->consultation_enable : 0;
                $reinventEnable = isset($setting->reinvent_enable) ? $setting->reinvent_enable : 0;
                return [
                    'consultation_enable' => $consultationEnable,
                    'reinvent_enable'     => $reinventEnable,

                ];
            } else {
                return [
                    'consultation_enable' => 0,
                    'reinvent_enable'     => 0,
                ];
            }

        }

        public function settingData(array $keys = [], $count = FALSE)
        {
            $data = Setting::whereIn('setting_key', [$keys]);
            if ($count) {
                $data = $data->count();
            } else {
                $data = $data->get(['setting_key', 'setting_value']);
            }
            return $data;
        }

        public function getVideoData()
        {
            $videoSetting = [];
            $settingData = $this->settingData(['video_meeting_api_setting']);
            if (isset($settingData)) {
                $video = collect($settingData)->firstWhere('setting_key', 'video_meeting_api_setting');
                if (isset($video->setting_value)) {
                    $videoSetting = json_decode($video->setting_value);
                }
            }
            return empty($videoSetting) ? FALSE : TRUE;
        }
    }

    