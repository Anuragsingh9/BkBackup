<?php

    namespace App\Http\Controllers;


    use App\Entity;
    use App\EntityUser;
    use App\Guest;
    use App\Http\Resources\UserSearchCollection;
    use App\Issuer;
    use App\Organisation;
    use App\Role;
    use App\Setting;
    use App\Signup;
    use App\Union;
    use App\User;
    use App\WorkshopMeta;
    use Artisan;
    use Auth;
    use Carbon\Carbon;
    use Config;
    use Cookie;
    use DB;
    use Hash;
    use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;
    use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
    use Hyn\Tenancy\Models\Hostname;
    use Hyn\Tenancy\Models\Website;
    use Illuminate\Http\Request;
    use Illuminate\Support\MessageBag;
    use Illuminate\Validation\Rule;
    use Modules\Crm\Services\NotesService;
    use Validator;
    use App\Workshop;
    use Illuminate\Support\Facades\App;


    class UserController extends Controller
    {

        private $core, $tenancy, $meeting, $mode, $notesService;

        public function __construct()
        {
            $this->notesService = NotesService::getInstance();
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);
            $this->core = app(\App\Http\Controllers\CoreController::class);
            $this->meeting = app(\App\Http\Controllers\MeetingController::class);
            $this->mode = env('APP_ENV');
            // set locale for localization
            App::setLocale((strtolower(session()->get('lang'))) ? strtolower(session()->get('lang')) : 'fr');
        }

        public function home()
        {
            return view('home');
        }

        public function reactApp()
        {

            // $domain = $_SERVER['SCRIPT_URL'];

            // $host = explode('.', $domain);
            // $domain = '';

            // if ($host[0] == 'www') {
            //     if ($host[1] == 'ooionline') {
            //         dd($host[1]);
            //     }
            // } else {
            //     if ($host[0] == 'ooionline') {
            //         dd($host[0]);
            //     }
            // }

            $domain = explode('/', $_SERVER['REQUEST_URI']);
            if (isset($domain[1]) && $domain[1] === 'referrer') {
                return view('ie_index');
            } elseif (!Auth::check()) {
                return redirect()->route('signin');
            }
            return view('ie_index');
        }

        public function tenancyDetail()
        {
            echo $this->tenancy->website();            // resolves the currently active website
            echo $hostname = $this->tenancy->hostname();           // resolves the currently active hostname
            echo $this->tenancy->customer();           // resolves the currently active customer
            substr('ops102.ert.opsimplify.com', 0, -15);
            //dd($hostname->fqdn);
            echo $hostname->fqdn;
            $this->tenancy->hostname($hostname);  // sets the currently active hostname
            $this->tenancy->identifyHostname();   // resets current hostname resolving to auto identification using request
        }

        public function redirectMeetingView(Request $request)
        {
            $hostname = $this->getHostData();
            if ($this->mode == 'local') {
                $redirectUrl = env('REACT_APP_URL') . str_rot13($request->url);
            } elseif ($this->mode == 'production' || $this->mode == 'staging' || $this->mode == 'testing') {
                $redirectUrl = env('HOST_TYPE') . $hostname->fqdn . '/#/' . str_rot13($request->url);
            } else {
                return redirect('mode-error')->back()->with('message', 'Mode not defined...');
            }
            session(['redirectUrl' => $redirectUrl]);
            if ($request->type == 'g') {
                $userId = base64_decode($request->userid);
                $userCount = User::where('id', $userId)->where('role', 'M3')->count();
                if ($userCount > 0 && Auth::loginUsingId($userId)) {
                    return redirect(session()->get('redirectUrl'));
                }
            } else {
                if (Auth::check()) {
                    return redirect(session()->get('redirectUrl'));
                } else {
                    return redirect()->route('signin');
                }
            }
        }

        function redirectGuestMeetingView($token)
        {
            $guest = Guest::where('identifier', $token)->first();
//        dd(Auth::check());
            $hostname = $this->getHostData();
            // $redirectUrl = config('constants.REACT_APP_URL') . str_rot13($request->url);
//$redirectUrl = config('constants.HOST_TYPE').$hostname->fqdn.'/#/'.str_rot13($request->url);
            //session(['redirectUrl' => $redirectUrl]);
            $userId = $guest->user_id;
            Auth::loginUsingId($userId);
            if (Auth::check()) {
                session()->put('guest', ['wid' => $guest->workshop_id, 'meeting_id' => $guest->meeting_id]);
                if ($guest->url_type == 'prepd') {
                    if ($this->mode == 'local') {
                        return redirect(env('REACT_APP_URL') . 'organiser/commissions/meeting/agenda/preparing-agenda');
                    } elseif ($this->mode == 'production' || $this->mode == 'staging' || $this->mode == 'testing') {
                        return redirect(env('HOST_TYPE') . $hostname->fqdn . '/#/organiser/commissions/meeting/agenda/preparing-agenda');
                    } else {
                        return redirect('mode-error')->back()->with('message', 'Mode not defined...');
                    }

                } elseif ($guest->url_type == 'repd') {
                    // return redirect(config('constants.HOST_TYPE') . $hostname->fqdn . '/#/organiser/commissions/meeting/dicision/prepare-statement');
                    if ($this->mode == 'local') {
                        return redirect(env('REACT_APP_URL') . 'organiser/commissions/meeting/dicision/prepare-statement');
                    } elseif ($this->mode == 'production' || $this->mode == 'staging' || $this->mode == 'testing') {
                        return redirect(env('HOST_TYPE') . $hostname->fqdn . '/#/organiser/commissions/meeting/dicision/prepare-statement');
                    } else {
                        return redirect('mode-error')->back()->with('message', 'Mode not defined...');
                    }

                } else {
                    if ($this->mode == 'local') {
                        return redirect(env('REACT_APP_URL') . 'organiser/commissions/meeting/view');
                    } elseif ($this->mode == 'production' || $this->mode == 'staging' || $this->mode == 'testing') {
                        return redirect(env('HOST_TYPE') . $hostname->fqdn . '/#/organiser/commissions/meeting/view');
                    } else {
                        return redirect('mode-error')->back()->with('message', 'Mode not defined...');
                    }

                }
            } else {
                return redirect()->route('signin');
            }
        }

        public function redirectAppUrl(Request $request)
        {
            $hostname = $this->getHostData();
            if ($this->mode == 'local') {
                $redirectUrl = env('REACT_APP_URL') . str_rot13($request->url);
            } elseif ($this->mode == 'production' || $this->mode == 'staging' || $this->mode == 'testing') {
                $redirectUrl = env('HOST_TYPE') . $hostname->fqdn . '/#/' . str_rot13($request->url);
            } else {
                return redirect('mode-error')->back()->with('message', 'Mode not defined...');
            }

//        $redirectUrl = config('constants.HOST_TYPE') . $hostname->fqdn . '/#/' . str_rot13($request->url);
            session(['redirectUrl' => $redirectUrl]);
            if (Auth::check() && Auth::user()->role != 'M3') {
//            $hostname = $this->getHostData();
                //$redirectUrl = config('constants.REACT_APP_URL').str_rot13($request->url);
//            $redirectUrl = config('constants.HOST_TYPE') . $hostname->fqdn . '/#/' . str_rot13($request->url);
//            session(['redirectUrl' => $redirectUrl]);
                if (Auth::check()) {
                    return redirect(session()->get('redirectUrl'));
                } else {
                    return redirect()->route('signin');
                }
            } else {
                return redirect()->route('signin');
            }
        }

        public function signupForm()
        {
            return view('signup');
        }

        public function loginForm(Request $request)
        {
            //dd(session()->all(),$request);
            if (Auth::check()) {
                return $this->redirectAppUrl($request);
            } else {
                return view('signin');
            }

        }

        public function loginUser(Request $request)
        {
            $hostname = $this->getHostData();
            $email = $request->email;
            $password = $request->password;
            if (Auth::check()) {
                Auth::logout();
            }
            //restrict user login for specific domain
            //    if($hostname->fqdn=='adn.opsimplify.com'){
            //        $emailArray=['ido26@live.fr','guillaume.sabatier@influencelesite.com',
            //            'adn@internetbusinessbooster.com',
            //            'danake@internetbusinessbooster.com',
            //             'danake@successfulconference.com',
            //             'danakedirect@gmail.com','benoit.senior@adnconstruction.org','f.bayle@untec.com',
            //             'dominique.riquiersauvage@orange.fr',
            //             'f.bayle@untec.com',
            //             'olivier.masseron@legrand.fr',
            //             'herve.gastaud@unge.net',
            //             'jf.hornain@capeb.fr'
            //        ];
            //         // $emailArray=[];
            //        if (in_array(strtolower($request->email),$emailArray)) {
            //            if (Auth::attempt(['email' => $email, 'password' => $password])) {
            //                $rUrl = $this->loginRedirect();
            //                return redirect($rUrl);
            //            } else {
            //                return redirect()->back()->with(['error' => config('constants.FLASH_INVALID_CREDENTIAL')]);
            //            }
            //        }else{
            //            return redirect('coming-soon');
            //        }
            //    }else{
            if (strtolower($request->email) && strtolower($request->password)) {
                if (Auth::attempt(['email' => $email, 'password' => $password])) {
                    session()->forget('staff');
                    if(Auth::user()->role=='M3'){
                        Auth::logout();
                        return redirect()->route('signin')->with('error', __('message.FLASH_INVALID_CREDENTIAL'))->withInput();
                    }
                    $rUrl = $this->loginRedirect();
//                    dd(Auth::user()->createToken('check'));
                    return redirect($rUrl);
                } else {
                    return redirect()->route('signin')->with('error', __('message.FLASH_INVALID_CREDENTIAL'))->withInput();
                }
            }
            //    }

        }

        public function changePasswordForm()
        {


            if (Auth::check()) {

                if (Auth::user()->login_count <= 1) {

                    if (session()->has('lang')) {
                        App::setLocale(strtolower(session('lang')));
                        $lang = 'forgot_password_' . session()->get('lang');
                    } else {
                        $lang = 'forgot_password_FR';
                    }
                    $data = getSettingData($lang);
                    $workshop = [];
                    if (Auth::user()->sub_role == 'C1') {
                        $WorkshopMeta = WorkshopMeta::where(['user_id' => Auth::user()->id, 'role' => 4])->first();
                        $workshop = Workshop::withoutGlobalScopes()->find($WorkshopMeta->workshop_id);
                    }
                    return view('change_password', compact('data', 'workshop'));
                } else {
                    Auth::logout();
                    return redirect('signin');
                }
            } else {
                return redirect('signin');
            }
        }

        public function changePasswordProcess(Request $request)
        {
            if (session()->has('lang')) {
                App::setLocale(strtolower(session('lang')));
            }
            if ($request->new_password == '' || $request->confirm_password == '')
                return redirect()->back()->with(['error' => __('message.FLASH_ALL_FIELD_REQUIRED')]);
            if (strlen($request->new_password) < 6)
                return redirect()->back()->with(['error' => __('message.FLASH_CPASS_LENGTH')]);
            if ($request->new_password != $request->confirm_password)
                return redirect()->back()->with(['error' => __('message.FLASH_NPASS_CPASS_NOT_MATCH')]);

            $result = User::where('id', Auth::user()->id)->update(['password' => Hash::make($request->new_password)]);
            if ($result) {
                User::where('id', Auth::user()->id)->increment('login_count');
                $rUrl = $this->loginRedirect();
//dd($rUrl);
                return redirect($rUrl);
            } else {
                return redirect()->back()->with(['error' => __('message.FLASH_INVALID_CREDENTIAL')]);
                return redirect()->back()->with(['error' => __('message.FLASH_RESET_PASS_FAIL')]);
            }
        }

        function loginRedirect()
        {
            $flag = 0;
            if (session()->get('redirectUrl')) {
                $flag = 1;
                if (Auth::user()->login_count == 0 && $flag == 1) {
                    User::where('id', Auth::user()->id)->increment('login_count');
                    return route('change-password');
                }
                $rUrl = session()->get('redirectUrl');
            } else {
                $flag = 0;
                if (Auth::attempt(['email' => Auth::user()->email, 'password' => Auth::user()->email]))
                    $flag = 1;
                if (Auth::user()->login_count == 0 && $flag == 1) {
                    User::where('id', Auth::user()->id)->increment('login_count');
                    return route('change-password');
                } else if (Auth::user()->sub_role == 'C1' /*&& Auth::user()->login_count == 0*/) {
                    $goTo = 'qualification/registration-form';
                } else if (Auth::user()->role == 'M2') {
                    $goTo = 'dashboard';
                } else if (Auth::user()->login_count > 1) {
                    $goTo = 'dashboard';
                } else {
                    if (Auth::user()->role == 'M1' && Auth::user()->login_count == 0){
                        User::where('id', Auth::user()->id)->increment('login_count');
                    }
                    $goTo = 'start';
                }
                $hostname = $this->getHostData();

                if ($this->mode == 'local') {
                    $rUrl = env('REACT_APP_URL') . $goTo;
                } elseif ($this->mode == 'production' || $this->mode == 'staging' || $this->mode == 'testing') {
                    $rUrl = env('HOST_TYPE') . $hostname->fqdn . '/#/' . $goTo;
                } else {
                    return redirect('mode-error')->back()->with('message', 'Mode not defined...');
                }
                //$rUrl = config('constants.REACT_APP_URL').$goTo;
            }

            return $rUrl;
        }

        function getHostData()
        {
            $this->tenancy->website();
            $hostdata = $this->tenancy->hostname();
            $domain = @explode('.' . config('constants.HOST_SUFFIX'), $hostdata->fqdn)[0];
            //$domain = config('constants.HOST_SUFFIX');
            session('hostdata', ['subdomain' => $domain]);
            return $this->tenancy->hostname();
        }

        public function signoutGuest()
        {
            Auth::logout();
            session()->flush();
            return redirect()->route('signin');
//        return response()->json(['status' => 1]);
        }

        public function signOut()
        {
            if (session()->has('data')) {
                session()->forget('staff');
                session()->forget('data');
                Auth::logout();
                $serverName = explode('.', $_SERVER['HTTP_HOST']);
                if (count($serverName) == 3) {
                    $url = $serverName[1] . '.' . $serverName[2];
                } elseif (count($serverName) == 2) {
                    $url = $serverName[0] . '.' . $serverName[1];
                } else {
                    $url = $_SERVER['HTTP_HOST'];
                }
                $redirectTo = $_SERVER['REQUEST_SCHEME'] . '://' . $url . '/get-account';
                return response()->json(['status' => 1, 'url' => $redirectTo]);
                return redirect()->away('ooionline.com/get-account');
                return redirect()->route('signin');
            } else {
                session()->forget('staff');
                session()->forget('data');
                Auth::logout();
                session()->flush();
                //return redirect()->route('signin');
                return response()->json(['status' => 1]);
            }

        }

        public function signupEmailForm()
        {
            return view('signup_email');
        }

        public function signupEmail(Request $request)
        {
            $userExist = Organisation::where('email', $request->email)->get();

            if (count($userExist) == 0) {
                $code = genRandomNum(6);
                $lastId = Signup::insertGetId(['email' => $request->email, 'code' => $code]);
                $mailData['mail'] = ['subject' => 'Verification de votre email', 'email' => $request->email, 'firstname' => 'BHEEM', 'otp' => $code, 'path' => url('signup/waiting_for_confirm?email=' . $request->email . '&userid=' . $lastId)];

                if ($this->core->SendEmail($mailData, 'welcome_email')) {
                    $lastId = Signup::insertGetId(['email' => $request->email, 'code' => $code]);
                    return redirect()->route('signup-verification', ['email' => $request->email, 'userid' => $lastId])->with(['success' => config('constants.FLASH_VERIFICATION_CODE_SEND')]);
                } else {
                    Signup::where('id', $lastId)->delete();
                    return redirect()->back()->with(['error' => __('message.FLASH_VERIFICATION_CODE_SEND_FAIL')]);
                }
            } else {
                return redirect()->back()->with(['error' => __('message.FLASH_EMAIL_ADDRESS_EXIST')]);
            }
        }

        public function resendEmail(Request $request)
        {
            $user = Signup::where('email', $request->email)->where('id', $request->id)->first();
            if (count($user) != 0) {
                $code = genRandomNum(6);
                //Verification de votre email
                $mailData['mail'] = ['subject' => 'Verification de votre email', 'email' => $request->email, 'firstname' => 'Sourabh', 'otp' => $code, 'path' => url('signup/waiting_for_confirm?email=' . $request->email . '&userid=' . $user['id'])];
                if ($this->core->SendEmail($mailData, 'welcome_email')) {
                    Signup::where('id', $request->id)->update(['code' => $code]);
                    return response()->json(['status' => 1]);
                } else {
                    return response()->json(['status' => 0]);
                }
            } else {
                return response()->json(['status' => 0]);
            }
        }

        public function signupVerification(Request $request)
        {
            if ($request->email != '' && $request->userid != '') {
                $res = Signup::where('email', $request->email)->where('id', $request->userid)->count();
                if ($res == 0) {
                    return view('errors/error_404');
                }
                $data['user_email'] = ['email' => $request->email];
                session()->forget('user_email');
                session($data);
                $email = $request->email;
            }

            return view('signup_verification', compact('email'));
        }

        public function signupSteps(Request $request)
        {
            if (chkUrlParams($request->key) == 0) {
                return view('errors/error_404');
            }
            return view('signup_steps');
        }

        function check_otp(Request $request)
        {
            $res = Signup::where('id', $request->id)->where('email', $request->email)->where('code', $request->code)->first();
            if ($res) {
                $token = 'security_token-' . $request->id . '-' . $request->code;
                $data['user_email'] = ['email' => $res->email];
                session()->forget('user_email');
                session($data);
                return response()->json(['status' => 1, 'key' => $token]);
            }
            return response()->json(['status' => 0]);
        }

        public function regStep(Request $request)
        {
            $status = 0;

            if ($request->step == 'one') {
                $data['personal_info'] = ["u_fname" => $request->u_fname, "u_lname" => $request->u_lname, "u_pass" => $request->u_pass];
                session($data);
                $status = 1;
            } else if ($request->step == 'two') {
                $data['organisation_info'] = ["name_org" => $request->name_org, "acronym" => $request->acronym,
                                              "sector"   => $request->sector, "permanent_member" => $request->permanent_member, "members_count" => $request->members_count];
                session($data);
                $status = 1;
            } else if ($request->step == 'three') {

                $status = 1;
                $domain = $request->domain;
                $domain_path = $domain . '.' . env('HOST_SUFFIX');

                if (DB::table('hostnames')->where('fqdn', $domain_path)->count() == 0) {
                    $website = new Website;

                    app(WebsiteRepository::class)->create($website);
                    $hostname = new Hostname;
                    $hostname->fqdn = $domain_path;
                    app(HostnameRepository::class)->attach($hostname, $website);

                    $data['account_info'] = ["domain" => $domain, "domain_path" => $domain . '.' . env('HOST_SUFFIX'), 'hostname' => $hostname];

                    session($data);
                    session('domain_name', substr($hostname->fqdn, 0, -15));
//                shell_exec('sudo certbot --non-interactive --agree-tos --email om.bissa@internetbusinessbooster.com --apache -d ' . $hostname->fqdn);
//                shell_exec('sudo /usr/sbin/service apache2 reload');

                } else {
                    $status = 'exist';
                }
            }
            return response()->json(['status' => $status]);
        }

        public function saveRegSteps(Request $request)
        {
            $status = 0;
            $url = '';
            if (session()->has('user_email') && session()->has('personal_info') && session()->has('account_info') != '') {
                $hostname = session()->get('account_info')['hostname'];
                //DB::beginTransaction();

                $lastId = Organisation::insertGetId([
                    'account_id'       => $hostname->id,
                    'fname'            => session()->get('personal_info')['u_fname'],
                    'lname'            => session()->get('personal_info')['u_lname'],
                    'password'         => Hash::make(session()->get('personal_info')['u_pass']),
                    'email'            => strtolower(session()->get('user_email')['email']),
                    'name_org'         => session()->get('organisation_info')['name_org'],
                    'acronym'          => session()->get('organisation_info')['acronym'],
                    'sector'           => session()->get('organisation_info')['sector'],
                    'members_count'    => session()->get('organisation_info')['members_count'],
                    'permanent_member' => session()->get('organisation_info')['permanent_member'],
                    'commissions'      => $request->commissions,
                    'working_groups'   => $request->groups,
                    'address1'         => ("55 Rue du Faubourg Saint-Honorè"),
                    'address2'         => '',
                    'postal_code'      => '75008',
                    'city'             => 'Paris',
                    'country'          => 'France',
                ]);

                if ($lastId > 0) {
                    $res = $this->domainSave($lastId);
                    if ($res > 0) {

                        /* if(Auth::attempt(['email' => session()->get('user_email')['email'], 'password' => session()->get('personal_info')['u_pass']]))
                          { */
                        $status = 1;
                        $url = env('HOST_TYPE') . session()->get('account_info')['domain_path'];
                        //session()->flush();
                        //}
                    } else {
                        $status = 2;
                        //DB::rollBack();
                    }
                }
                //DB::commit();
            }
            $check1 = Artisan::call('migrate', ['--database' => 'tenant']);
            $check = Artisan::call('db:seed', ['--database' => 'tenant']);
            Artisan::call('module:seed', ['--database' => 'tenant']);
            return response()->json(['status' => $status, 'redirect' => $url]);
        }

        function domainSave($org_id)
        {
            $hostname = session()->get('account_info')['hostname'];
            $domain = session()->get('account_info')['domain'];
            $domain_path = session()->get('account_info')['domain_path'];
//creating a entry in hostname_codes
            $hashRand = generateRandomValue(3, 1);
            $checkCodeUnique = DB::connection('mysql')->table('hostname_codes')->where('hash', $hashRand)->count();
            $checkDomain = DB::connection('mysql')->table('hostname_codes')->where('fqdn', $hostname->fqdn)->count();
            if ($checkDomain == 0 && $checkCodeUnique == 0) {
                $addHostCode = DB::connection('mysql')->table('hostname_codes')->insert(['fqdn' => $hostname->fqdn, 'hash' => $hashRand]);
            } else {
                if ($checkDomain == 0 && $checkCodeUnique != 0) {
                    $addHostCode = DB::connection('mysql')->table('hostname_codes')->insert(['fqdn' => $hostname->fqdn, 'hash' => $this->checkUnique()]);
                }
            }

            //creating entry in weekly Reminder table
            $addWeekly = DB::connection('mysql')->table('weekly_reminders')->insert(['fqdn' => $hostname->fqdn, 'status' => 0, 'on_off' => 1]);

            $orgData = DB::connection('mysql')->table('organisation')->find($org_id);
            if ($orgData) {

                $this->tenancy->hostname($hostname);
                $this->tenancy->identifyHostname();
                $this->core->makeDirectoryS3($domain);
                $query = "update organisation set account_id =" . $hostname->id . " where id = ?";
                DB::connection('mysql')->update($query, [$orgData->id]);
                $org = Organisation::insertGetId([
                    'account_id'       => $orgData->account_id,
                    'fname'            => session()->get('personal_info')['u_fname'],
                    'lname'            => session()->get('personal_info')['u_lname'],
                    'password'         => Hash::make(session()->get('personal_info')['u_pass']),
                    'email'            => session()->get('user_email')['email'],
                    'name_org'         => session()->get('organisation_info')['name_org'],
                    'acronym'          => session()->get('organisation_info')['acronym'],
                    'sector'           => session()->get('organisation_info')['sector'],
                    'members_count'    => session()->get('organisation_info')['members_count'],
                    'permanent_member' => session()->get('organisation_info')['permanent_member'],
                    'commissions'      => $orgData->commissions,
                    'working_groups'   => $orgData->working_groups,
                    'address1'         => ('55 Rue du Faubourg Saint-Honoré'),
                    'address2'         => '',
                    'postal_code'      => '75008',
                    'city'             => 'Paris',
                    'country'          => 'France',
                ]);

                Issuer::insert(['issuer_name' => $orgData->name_org, 'issuer_code' => strtoupper($orgData->acronym)]);

                //change signature in email_graphics
                $ordData = Organisation::find($org);
                $setting = Setting::where('setting_key', 'email_graphic')->first();
                $json_decode = json_decode($setting->setting_value);
                $json_decode->email_sign = $ordData->fname . ' ' . $ordData->lname . ' <br />' . $ordData->email;
                $setting->setting_value = json_encode($json_decode);
                $setting->save();

                //changing pdfGraphic Setting
                $pre_data = getSettingData('graphic_config', 1);
                $postData = ['color1' => ($pre_data->color1), 'color2' => ($pre_data->color2), 'footer_line1' => '', 'footer_line2' => ''];
                $postData['header_logo'] = $pre_data->header_logo;
                $setting1 = Setting::where('setting_key', 'pdf_graphic')->first();
                $setting1->setting_value = json_encode($postData);
                $setting1->save();


                $settingsData = [
                    'account_id'        => $orgData->account_id,
                    'test_version'      => 1,
                    'light_version'     => 1,
                    'mobile_enable'     => 1,
                    'wvm_enable'        => 1,
                    'fvm_enable'        => 1,
                    'travel_enable'     => 1,
                    'user_group_enable' => 0,
                    'wiki_enable'       => 1,
                    'reminder_enable'   => 1,
                    'zip_download'      => 1,
                    'fts_enable'        => 1,
                    'repd_connect_mode' => 1,
                    'prepd_repd_notes'  => 1,
                    'date_from'         => date("Y-m-d H:i:s"),
                    'date_to'           => date("Y-m-d H:i:s", strtotime('+30 days')),
                ];

                DB::connection('mysql')->table('account_settings')->insert($settingsData);
                $hostCode = DB::connection('mysql')->table('hostname_codes')->where(['fqdn' => $hostname->fqdn])->first(['hash']);
                $randCode = generateRandomValue(3);
                $newCode = setPasscode($hostCode->hash, $randCode);

                $userArray = [
                    'fname'          => $orgData->fname,
                    'lname'          => $orgData->lname,
                    'email'          => $orgData->email,
                    'password'       => $orgData->password,
                    'role'           => 'M1',
                    'role_commision' => 1,
                    'role_wiki'      => 1,
                    'login_count'    => 0,
                    'login_code'     => $newCode['userCode'],
                    'hash_code'      => $newCode['hashCode'],
                ];
                $url = env('HOST_TYPE') . session()->get('account_info')['domain_path'];
                $mailData['mail'] = ['subject' => 'Enregistrement terminé', 'email' => session()->get('user_email')['email'], 'firstname' => 'BHEEM', 'user' => session()->get('personal_info')['u_fname'], 'organization' => session()->get('organisation_info')['name_org'], 'path' => $url];
                // $mailData['mail']['email']=utf8_encode($mailData['mail']['email']);
                // $mailData['mail']['organization']=utf8_encode($mailData['mail']['organization']);
                $this->core->SendEmail($mailData, 'register_email');
                // return response($this->core->SendEmail($mailData,'register_email'));
                $id = User::insertGetId($userArray);

                return $id;
            } else {
                return 0;
            }
        }

        public function forgetPassView(Request $request)
        {
            return view('forgot_password');
        }

        public function forgetPassword(Request $request)
        {
            if (session()->has('lang')) {
                App::setLocale(strtolower(session('lang')));
            }
            $user = User::where('email', $request->email)->where('role','!=', 'M3')->first();

            if (isset($user->id)) {
                $key = generateRandomString(36);
                $user->identifier = $key;
                $user->save();
                $link = route('reset-password', ['userid' => $request->email, 'token' => $key]);
                /* $mailData['mail'] = ['subject' => 'Réinitialisation de votre mot de passe', 'email' => $request->email, 'firstname' => 'OP Simplify', 'msg' => 'To reset your password, visit the following address : <a href="' . $link . '" target="_blank">Click here to reset your password</a>.'];*/
                $mailData['mail'] = ['subject' => "Réinitialisation de votre mot de passe", 'email' => $request->email, 'firstname' => 'OP Simplify', 'url' => $link, 'user' => $user];
//            $url = config('constants.HOST_TYPE') . session()->get('account_info')['domain_path'];

                if ($this->core->SendEmail($mailData, 'forget-password')) {
                    return redirect('signin')->with('success', __('message.FLASH_RESET_PASS_LINK_SEND'));

                } else {
                    return redirect()->back()->with(['error' => __('message.FLASH_RESET_PASS_LINK_SEND_FAIL')]);
//                return redirect()->back()->with(['error' => config('constants.FLASH_RESET_PASS_LINK_SEND_FAIL')]);
                }
            } else {
                return redirect()->back()->with(['error' => __('message.FLASH_INVALID_EMAIL_ADDRESS')]);
            }
        }

        public function resetPassView(Request $request)
        {
            return view('reset_password');
        }

        public function resetPassword(Request $request)
        {
            if (session()->has('lang')) {
                App::setLocale(strtolower(session('lang')));
            }

            if ($request->identifier == '' || $request->new_password == '' || $request->confirm_password == '')
                return redirect()->back()->with(['info' => __('message.FLASH_ALL_FIELD_REQUIRED')]);
            if (strlen($request->new_password) < 6)
                return redirect()->back()->with(['error' => __('message.FLASH_CPASS_LENGTH')]);

            if ($request->new_password != $request->confirm_password)
                return redirect()->back()->with(['error' => __('message.FLASH_NPASS_CPASS_NOT_MATCH')]);

            if (User::where('identifier', $request->identifier)->count() == 1) {
                User::where('identifier', $request->identifier)->update(['password' => Hash::make($request->new_password), 'identifier' => NULL]);
                return redirect()->route('signin')->with(['success' => __('message.FLASH_RESET_PASS_SUCCESS')]);
            } else {
                return redirect()->back()->with(['error' => __('message.FLASH_RESET_PASS_FAIL')]);
            }
        }

        public function addUser(Request $request)
        {

            $mail_check = User::where('email', strtolower($request->email))->where('role', 'M3')->first();
            if (isset($mail_check->id)) {
                if ($mail_check['role'] == 'M3') {
                    if ($request->password == $request->confirm_password) {
                        if (!isset($request->password)) {
                            $user_password = strtolower($request->email);
                        } else {
                            $user_password = ($request->email == $request->password) ? strtolower($request->email) : $request->confirm_password;
                            $request['password'] = ($request->password == '') ? $user_password : $user_password;
                        }

                        if ($request->hasFile('image')) {
                            $domain = strtok($_SERVER['SERVER_NAME'], '.');
                            $folder = $domain . '/uploads/user_profile';
                            $filename = $this->core->fileUploadByS3($request->file('image'), $folder, 'public');
                            $request->merge(['avatar' => $filename]);
                        }
                        $request->merge(['password' => Hash::make($user_password)]);
                        $except = ['image', 'confirm_password', 'entity_id'];
                        $except[] = 'function_society';
                        if ($request->industry_id == 'undefined')
                            $except[] = 'industry_id';
                        if ($request->family_id == 'undefined')
                            $except[] = 'family_id';
                        if ($request->union_id == 'undefined')
                            $except[] = 'union_id';
                        // if($request->role==null && $request->role_commision=='1'){
                        //     $request->role = 'W0';
                        // }
                        $request['email'] = strtolower($request->email);
                        $res = User::where('email', $mail_check['email'])->update($request->except($except));
                        if ($res > 0) {
                            $user = User::where('email', $request->email)->first();
                            if (isset($request->entity_id)) {
                                $entityUser = EntityUser::updateOrCreate(['user_id' => $user->id, 'entity_id' => $request->entity_id], ['entity_label' => $request->function_society, 'entity_id' => $request->entity_id, 'created_by' => Auth::user()->id]);

                            }

                            $params = ['emails' => $mail_check['email'], 'user_id' => 0, 'user_type' => 'm', 'user_password' => $user_password];
                            $result_data = $this->meeting->sendNewMemberInvitationEmail($params);
                            return response()->json(['status' => 1, 'msg' => "User Added Successfully.", 'data' => $user]);
                        } else {
                            return response()->json(['status' => 0, 'msg' => "User Insertion Failed."]);
                        }
                    } else {
                        return response()->json(['status' => 0, 'msg' => "Password Dosn't Match."]);
                    }
                } else {
                    return response()->json(['status' => 0, 'msg' => "Email id already exsit."]);
                }
            } else {
                $count = User::where('email', strtolower($request->email))->count();
                if ($count == 0) {
                    if ($request->password == $request->confirm_password) {
                        if (!isset($request->password)) {
                            $user_password = strtolower($request->email);
                        } else {
                            $user_password = ($request->email == $request->password) ? strtolower($request->email) : $request->confirm_password;
                        }
                        $request['password'] = ($request->password == '') ? $user_password : $user_password;
                        if ($request->hasFile('image')) {
                            $domain = strtok($_SERVER['SERVER_NAME'], '.');
                            $folder = $domain . '/uploads/user_profile';
                            $filename = $this->core->fileUploadByS3($request->file('image'), $folder, 'public');
                            $request->merge(['avatar' => $filename]);
                        }
                        $request->merge(['password' => Hash::make($user_password)]);
                        $except = ['image', 'confirm_password', 'entity_id'];
                        $except[] = 'function_society';
                        if ($request->industry_id == 'undefined')
                            $except[] = 'industry_id';
                        if ($request->family_id == 'undefined')
                            $except[] = 'family_id';
                        if ($request->union_id == 'undefined')
                            $except[] = 'union_id';
                        // if($request->role==null && $request->role_commision=='1'){
                        //     $request->role = 'W0';
                        // }
                        $hostname = $this->getHostNameData();
                        $hostCode = DB::connection('mysql')->table('hostname_codes')->where(['fqdn' => $hostname->fqdn])->first(['hash']);
                        $randCode = generateRandomValue(3);

                        $newCode = setPasscode($hostCode->hash, $randCode);
                        $request->merge(['login_code' => $newCode['userCode']]);
                        $request->merge(['hash_code' => $newCode['hashCode']]);
                        //this is adding default role
                        if (!isset($request->role)) {
                            $request->merge(['role' => 'M2']);
                        }

                        if (!isset($request->permissions)) {
                            $request->merge(['permissions' => '{"crmAdmin":0,"crmEditor":0,"crmAssistance":0,"crmRecruitment":0}']);
                        }
//var_dump($request->except($except));exit;
                        $request['email'] = strtolower($request->email);
                        if (isset($request->first_name)) {
                            $request->merge(['fname' => $request->first_name]);
                            $request->request->remove('first_name');
                        }
                        if (isset($request->last_name)) {
                            $request->merge(['lname' => $request->last_name]);
                            $request->request->remove('last_name');
                        }


                        $res = User::insert($request->except($except));
                        if ($res) {
                            $user = User::where('email', $request->email)->first();
                            if (isset($request->entity_id)) {
                                $entityUser = EntityUser::updateOrCreate(['user_id' => $user->id, 'entity_id' => $request->entity_id], ['entity_label' => $request->function_society, 'entity_id' => $request->entity_id, 'created_by' => Auth::user()->id]);
                            }
                            if (session()->get('lang') == 'EN')
                                $note = 'Person created on ' . getCreatedAtAttribute(Carbon::now()) . ' by ' . (Auth::user()->fname . ' ' . Auth::user()->lname);
                            else
                                $note = 'Personne créée le ' . getCreatedAtAttribute(Carbon::now()) . ' par ' . (Auth::user()->fname . ' ' . Auth::user()->lname);
                            $this->notesService->addNote(['type' => 'user', 'field_id' => $user->id, 'notes' => $note]);

                            $params = ['emails' => $request->email, 'user_id' => 0, 'user_type' => 'm', 'user_password' => $user_password];
                            $result_data = $this->meeting->sendNewMemberInvitationEmail($params);
                            return response()->json(['status' => 1, 'msg' => "User Added Successfully.", 'data' => $user]);
                        } else {
                            return response()->json(['status' => 0, 'msg' => "User Insertion Failed."]);
                        }
                    } else {
                        return response()->json(['status' => 0, 'msg' => "Password Dosn't Match."]);
                    }
                } else {
                    return response()->json(['status' => 0, 'msg' => "Email already Exists."], 422);
                }
            }
        }

        public function editUser(Request $request)
        {
            $domain = strtok($_SERVER['SERVER_NAME'], '.');
            if ($request->hasFile('image')) {
                $folder = $domain . '/uploads/user_profile';
                $filename = $this->core->fileUploadByS3($request->file('image'), $folder, 'public');
                $request->merge(['avatar' => $filename]);
            }
            $except[] = 'email';
            $except[] = 'image';
            $except[] = 'entity_id';
            $except[] = 'function_society';
            if ($request->password == 'undefined' || $request->password == NULL) {
                $except[] = 'password';
            } else {
                $request->merge(['password' => Hash::make($request->password)]);
            }
            if ($request->industry_id == 'undefined')
                $except[] = 'industry_id';
            if ($request->family_id == 'undefined')
                $except[] = 'family_id';
            if ($request->union_id == 'undefined')
                $except[] = 'union_id';
            //these 3 if will removed in crm,currently issuing in edit
            if (isset($request->instance_id))
                $except[] = 'instance_id';
            if (isset($request->instance_position))
                $except[] = 'instance_position';
            if (isset($request->instance))
                $except[] = 'instance';
            $res = User::where('id', $request->id)->update($request->except($except));
            if ($res) {
                if (!empty($request->entity_id)) {
                    $user = User::find($request->id);
                    $entityUser = EntityUser::updateOrCreate(['user_id' => $user->id], ['entity_label' => $request->function_society, 'entity_id' => $request->entity_id, 'created_by' => Auth::user()->id]);
                }
                // $entityUser = EntityUser::where(['user_id' => $user->id])->first();

                // if($entityUser) {

                // $entity = Entity::where(['id' => $entityUser->entity_id])->update(['long_name' => $user->society, 'short_name' => $user->society, 'address1' => $user->address, 'address2' => $user->fqdn, 'zip_code' => $user->postal, 'city' => $user->city, 'country' => $user->country, 'phone' => $user->phone, 'email' => $user->email, 'entity_type_id' => 2]);
                // }else{
                // $entity = Entity::create(['long_name' => $user->society, 'short_name' => $user->society, 'address1' => $user->address, 'address2' => $user->fqdn, 'zip_code' => $user->postal, 'city' => $user->city, 'country' => $user->country, 'phone' => $user->phone, 'email' => $user->email, 'entity_type_id' => 2,'created_by'=>Auth::user()->id]);

                // EntityUser::create(['user_id' => $user->id, 'entity_id' => $entity->id,'created_by'=>Auth::user()->id, 'entity_label' => '']);
                //}
                return response()->json(['status' => 1, 'msg' => "User Update Successfully.", 'response' => User::find($request->id)]);
            } else
                return response()->json(['status' => 0, 'msg' => "User Update Failed."]);
        }

        public function changePassword(Request $request)
        {
            if (session()->has('lang')) {
                App::setLocale(strtolower(session('lang')));
            }

            $response = ['status' => 0, 'msg' => __('message.FLASH_CPASS_LENGTH')];
            if ($request->new_password == $request->confirm_password) {

                if (Hash::check($request->current_password, Auth::user()->password)) {
                    //Change Password
                    $user = Auth::user();
                    $user->password = bcrypt($request->new_password);
                    $user->save();
                    //User::where('id', $request->id)->update(['password' => bcrypt($request->new_password)]);
                    $response = ['status' => 1, 'msg' => __('message.FLASH_RESET_PASS_SUCCESS')];
                    $key = generateRandomString(36);
                    User::where('id', $request->id)->update(['identifier' => $key]);
                    $link = route('reset-password', ['userid' => Auth::user()->email, 'token' => $key]);
                    $mailData['mail'] = ['subject' => 'Modification de votre mot de passe', 'email' => Auth::user()->email, 'firstname' => Auth::user()->fname, 'url' => $link];
                    $this->core->SendEmail($mailData, 'password-change');
                } else {
                    $response = ['status' => 0, 'msg' => __('message.FLASH_CURRENT_PASS_FAIL')];
                }

            } else {
                $response = ['status' => 0, 'msg' => __('message.FLASH_NPASS_CPASS_NOT_MATCH')];
            }

            return response($response);
        }

        public function searchUser(Request $request)
        {

            User::$preventAttrGet = FALSE;
            $query = User::query();
            if ($request->user_name != '') {
                $query->where(function ($q) use ($request) {
                    $q->orWhere('fname', 'like', '%' . $request->user_name . '%')->orWhere('lname', 'like', '%' . $request->user_name . '%')->orWhere('email', 'like', '%' . $request->user_name . '%');
                });
            }
            if ($request->role != '') {
                switch ($request->role) {
                    case 'C1':
                        $query->whereRaw('JSON_CONTAINS(permissions, \'{"crmAdmin": 1}\')');
                        break;
                    case 'C2':
                        $query->whereRaw('JSON_CONTAINS(permissions, \'{"crmEditor": 1}\')');
                        break;
                    case 'C3':
                        $query->whereRaw('JSON_CONTAINS(permissions, \'{"crmFinance": 1}\')');
                        break;
                    case 'C4':
                        $query->whereRaw('JSON_CONTAINS(permissions, \'{"crmRecruitment": 1}\')');
                        break;
                    case 'C5':
                        $query->whereRaw('JSON_CONTAINS(permissions, \'{"crmAssistance": 1}\')');
                        break;
                    case 'W0':
                        $meta = WorkshopMeta::where('role', 1)->groupBy('user_id')->pluck('user_id')->toArray();
                        // echo implode(',',$meta);die;
                        $query->whereIn('id', $meta);
                        break;

                    case 'W1':
                        $meta = WorkshopMeta::where('role', 2)->groupBy('user_id')->pluck('user_id');
                        $query->whereIn('id', $meta);
                        break;

                    default:
                        $query->where('role', $request->role);
                        break;
                }
                // if ($request->role == 'W0') {
                //     $query->where('role_commision', 1);
                // }elseif(){}
                // else {
                //     $query->where('role', $request->role);
                // }
            }
            if ($request->union != '') {
                $query->where('union_id', $request->union);
            }

            if ($request->start_date != '' && $request->end_date != '') {
                $query->whereBetween(DB::raw('date(created_at)'), [date('Y-d-m', strtotime($request->start_date)), Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d')]);
            } else {
                if ($request->start_date != '') {
                    $query->where(DB::raw('date(created_at)'), '>=', date('Y-m-d', strtotime($request->start_date)));
                }
                if ($request->end_date != '') {
                    $query->where(DB::raw('date(created_at)'), '<=', date('Y-m-d', strtotime($request->end_date)));
                }
            }
            //condition if union and society searched
            if (!empty($request->union) || !empty($request->society)) {
                $query->whereHas('entity', function ($a) use ($request) {
                    if ($request->union != '') {
                        $a->where('entities.id', $request->union)->where('entity_type_id', 3);
                    }
                    if ($request->society != '') {
                        $a->where('entities.id', $request->society)->where('entity_type_id', 2);
                    }
                });
            }

            //$serachRes = $query->groupBy('email')->orderBy('lname','asc')->with('union')->with('role')->get();
            $pageno = (isset($request->pageno) ? $request->pageno : 10);
            $no_of_records_per_page = (isset($request->perPage) ? $request->perPage : 10);
            $offset = ($pageno - 1) * $no_of_records_per_page;

            $serachRes = $query->select('users.id', 'email', 'fname', 'lname', 'created_at', 'union_id', 'family_id', 'industry_id', 'society', 'internal_id', 'sub_role', 'permissions', 'setting', 'on_off', 'role')->groupBy('email','users.id')->orderBy('id', 'asc')->with(['union' => function ($q) {
                $q->select('id', 'union_code');
            }, 'entity'                                                                                                                                                                                                                                                       => function ($q) use ($request) {
                $q->select('entities.id', 'entities.long_name', 'entities.entity_type_id');
            }, 'role', 'userMeta.user'                                                                                                                                                                                                                                        => function ($q) {
                $q->select('id');
            }])->where(function ($a) {
                $a->where('sub_role', '!=', 'C1');
                $a->orWhereNull('sub_role');
            })->get();

            //paginate((isset($request->perPage)?$request->perPage:10))
            return new UserSearchCollection($serachRes);
            //['id','email','fname','lname','created_at','union_id','function_union','function_society','family_id','industry_id','society']
            return response()->json($serachRes);
        }

        public function getUser(Request $request)
        {
            $data = User::all();
            return response()->json($data);
        }

        public function getUserById($id)
        {
            if ($id == 0) {
                $id = Auth::user()->id;
            }
            $res = User::with(['entity' => function ($q) {
                $q->select('entities.id', 'entities.long_name', 'entities.entity_type_id');
            }, 'entity.entityLabel'     => function ($a) use ($id) {
                $a->where('user_id', $id);
            }])->find($id);
            if ($res != NULL) {
                if ($res->avatar != '')
                    $res->avatar = $this->core->getS3Parameter($res->avatar, 2);
                return response()->json($res);
            }
        }

        public function getUserRoles(Request $request)
        {
            $hostname = app(\Hyn\Tenancy\Environment::class)->hostname();
            $superPermission = \DB::connection('mysql')->table('account_settings')->where('account_id', $hostname->id)->first(['crm_menu_enable']);

            if (!$superPermission->crm_menu_enable) {
                $data = Role::whereNotIn('role_key', ['C1', 'C2', 'C3', 'C4', 'C5'])->get();
                return response()->json($data);
            }

            $data = Role::get();
            return response()->json($data);
        }

        public function getFilterdUser($val)
        {
            $data = User::where('role', '!=', 'M3')->where(function ($a) {
                $a->where('sub_role', '!=', 'C1');
                $a->orWhereNull('sub_role');
            })->where(DB::raw('CONCAT(email," ", lname," ",fname)'), 'like', '%' . $val . '%')->groupBy('email')->get();
            return response()->json($data);
        }

        public function getFilterdValidator(Request $request)
        {
            /* $data = User::where('id','!=',$request->user_id)->where(function($q) use($request){
              $q->where('fname','like','%'.$request->val.'%')->orWhere('email','like','%'.$request->val.'%');
              })->get(); */
            $data = User::where(function ($q) use ($request) {
                $q->where('role_commision', 1)->orWhereIn('role', ['M1', 'M0']);
            })->where(DB::raw('CONCAT(fname," ", lname)'), 'like', '%' . $request->val . '%')->get();
            return response()->json($data);
        }

        public function getFilterdPresident(Request $request)
        {
            /* $data = User::where('id','!=',$request->user_id)->where('role_commision',1)->where(function($q) use($request){
              $q->where('fname','like','%'.$request->val.'%')->orWhere('email','like','%'.$request->val.'%');
              })->get(); */
            $data = User::where(function ($q) use ($request) {
                $q->where('role_commision', 1)->orWhereIn('role', ['M1', 'M0']);
            })->where(DB::raw('CONCAT(fname," ", lname)'), 'like', '%' . $request->val . '%')->get();
            return response()->json($data);
        }

        public function getWorkshopUsers(Request $request)
        {

            $userIds = WorkshopMeta::where('workshop_id', $request->wid)->pluck('user_id', 'id');
            $data = User::whereIn('id', $userIds)->where(function ($q) use ($request) {
                $q->orWhere('fname', 'like', '%' . $request->val . '%')
                    ->orWhere('lname', 'like', '%' . $request->val . '%')
                    ->orWhere('email', 'like', '%' . $request->val . '%');
            })->where('role', '!=', 'M3')->groupBy('email')->get(['id', 'fname', 'lname', 'avatar', 'email', 'avatar']);
            return response()->json($data);
        }

        public function getSociety($val)
        {
            $entity = Entity::where('long_name', 'LIKE', $val . '%')->where('entity_type_id', 2)->get(['id', 'long_name']);
            return response()->json($entity);
//            $data = User::whereHas('entity', function ($a) use ($val) {
//                $a->where('long_name', 'like', '%' . $val . '%');
//            })->get();
//            // $data = User::where('society', 'like', '%' . $val . '%')->get();
//            return response()->json($data);
//            $serachRes = User::whereHas('entity', function ($a) use ($val) {
//                $a->where('long_name', 'like', '%' . $val . '%');
//            })->select('users.id', 'email', 'fname', 'lname', 'created_at', 'union_id', 'family_id', 'industry_id', 'society', 'internal_id', 'sub_role', 'permissions', 'setting', 'on_off', 'role')->groupBy('email')->orderBy('id', 'asc')->with(['union' => function ($q) {
//                $q->select('id', 'union_code');
//            }, 'entity'                                                                                                                                                                                                                                      => function ($q) {
//                $q->select('entities.id', 'entities.long_name', 'entities.entity_type_id');
//            }, 'role', 'userMeta.user'                                                                                                                                                                                                                       => function ($q) {
//                $q->select('id');
//            }])->where(function ($a) {
//                $a->where('sub_role', '!=', 'C1');
//                $a->orWhereNull('sub_role');
//            })->get();
//            //paginate((isset($request->perPage)?$request->perPage:10))
//            return new UserSearchCollection($serachRes);
        }

        public function getUnions(Request $request)
        {
//            $data = Union::get(['union_name', 'id']);
//            return response()->json($data);
            $entity = Entity::where('entity_type_id', 3)->get(['id', 'long_name as union_name']);
            return response()->json($entity);
        }

        public function delUser($id)
        {
            User::where('id', $id)->delete();
            return response()->json(1);
        }

        public function checkDbEntry()
        {
            //changing pdfGraphic Setting
            $pre_data = getSettingData('graphic_config', 1);
            $postData = ['color1' => ($pre_data->color1), 'color2' => ($pre_data->color2), 'footer_line1' => '', 'footer_line2' => ''];
            $postData['header_logo'] = $pre_data->header_logo;
            $setting = Setting::where('setting_key', 'pdf_graphic')->first();
            $setting->setting_value = json_encode($postData);
            $setting->save();
            /*$org = Organisation::insertGetId([
                'account_id' => 'test',
                'fname' => 'test',
                'lname' => 'test',
                'password' => Hash::make(session()->get('personal_info')['u_pass']),
                'email' => 'test@gmail.com',
                'name_org' =>'test',
                'acronym' => 'test',
                'sector' => 'test',
                'members_count' => 'test',
                'permanent_member' => 'test',
                'commissions' => 'Test',
                'working_groups' => 'Test',
                'address1' => ("55 Rue du Faubourg Saint-Honorè"),
                'address2' => '',
                'postal_code' => '75008',
                'city' => 'Paris',
                'country' => 'France'
            ]);*/
            //change signature in email_graphics
            $ordData = Organisation::find(1);
            $setting = Setting::where('setting_key', 'email_graphic')->first();
            $json_decode = json_decode($setting->setting_value);
            $json_decode->email_sign = $ordData->fname . ' ' . $ordData->lname . ' <br />' . $ordData->email;
            $setting->setting_value = json_encode($json_decode);
            $setting->save();
            dd($setting);

        }

        public function fileDelete($id)
        {
            $user = User::find($id);
            $this->core->fileDeleteBys3($user->avatar);
            $res = User::where('id', $id)->update(['avatar' => '']);
            if ($res) {
                $user = User::find($id);
                return response()->json(['status' => 1, 'msg' => "User Profile Delete Successfully.", 'response' => $user]);
            } else
                return response()->json(['status' => 0, 'msg' => "User Profile Delete Failed."]);
        }

        public function adnLoginForm()
        {
            return view('adn_signin');
        }

        public function carteProLoginForm()
        {
            return view('cartepro_signin');
        }

        public function submitAdnLoginForm(Request $request)
        {

            $email = $request->email;
            $password = $request->password;
            $hostname = $this->getHostData();

            if (strtolower($request->email) && strtolower($request->password)) {
                if (Auth::attempt(['email' => $email, 'password' => $password])) {
                    $rUrl = $this->loginRedirect();

                    echo '<script> var url="https://adn.opsimplify.com/#/dashboard"; window.open(url, "_blank");</script>';
                    // echo '<script>window.parent.location.href="https://adn.opsimplify.com/#/dashboard";</script>';

                    //return redirect($rUrl);
                } else {
                    return redirect()->back()->with(['error' => __('message.FLASH_INVALID_CREDENTIAL')]);
                }
            }
        }

        public function submitCarteProLoginForm(Request $request)
        {

            $email = $request->email;
            $password = $request->password;
            $hostname = $this->getHostData();

            if (strtolower($request->email) && strtolower($request->password)) {
                if (Auth::attempt(['email' => $email, 'password' => $password])) {
                    $rUrl = $this->loginRedirect();

                    echo '<script> var url="https://cartetppro.ooionline.com//#/dashboard"; window.open(url, "_blank");</script>';
                    // echo '<script>window.parent.location.href="https://adn.opsimplify.com/#/dashboard";</script>';

                    //return redirect($rUrl);
                } else {
                    return redirect()->back()->with(['error' => __('message.FLASH_INVALID_CREDENTIAL')]);
                }
            }
        }

        public function adnForgetPassword()
        {
            echo '<script>window.parent.location.href="https://adn.opsimplify.com/forgot-password";</script>';
        }


        function getHostNameData()
        {
            $this->tenancy->website();
            $hostdata = $this->tenancy->hostname();
            $domain = @explode('.' . config('constants.HOST_SUFFIX'), $hostdata->fqdn)[0];
            //$domain = config('constants.HOST_SUFFIX');
            session('hostdata', ['subdomain' => $domain]);
            return $this->tenancy->hostname();
        }

        public function getCompanies($val)
        {
            $entity = Entity::where('long_name', 'LIKE', $val . '%')->where('entity_type_id', 2)->get(['id', 'long_name']);
            return response()->json($entity);
        }


        public function createCompanies(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'long_name' => ['required', Rule::unique('tenant.entities')->where(function ($query) use ($request) {
                    return $query->where('entity_type_id', 2);
                })],
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => FALSE,
                    'msg'    => implode(',', $validator->errors()->all()),
                ], 422); //validation false return errors
            }
            $entity = Entity::create(['long_name' => $request->long_name, 'entity_type_id' => 2]);
            $entityUser = EntityUser::updateOrCreate(['user_id' => $request->id], ['entity_label' => '', 'entity_id' => $entity->id]);
            return response()->json($entity);
        }

        public function createCompaniesNew(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'long_name' => ['required', Rule::unique('tenant.entities')->where(function ($query) use ($request) {
                        return $query->where('entity_type_id', ($request->has('type') && $request->type == 'instance') ? 1 : 2);
                    })],
                    'logo'      => 'nullable|image',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422); //validation false return errors
                }


                if ($request->hasFile('logo')) {
                    $domain = strtok($_SERVER['SERVER_NAME'], '.');
                    $folder = $domain . '/uploads/' . $request->type;

                    $filename = $this->core->fileUploadByS3($request->file('logo'), $folder, 'public');
                    $request->merge(['entity_logo' => $filename]);
                }
                if (empty($request->address2)) {
                    $request->address2 = NULL;
                }
                if (empty($request->industry_id)) {
                    $request->industry_id = NULL;
                }
                if ($request->has('type') && $request->type == 'instance') {
                    $entity = Entity::create([
                        'long_name'          => $request->long_name,
                        'entity_type_id'     => 1,
                        'short_name'         => $request->short_name,
                        'email'              => $request->email,
                        'entity_website'     => $request->website,
                        'address1'           => $request->address,
                        'city'               => $request->city,
                        'country'            => $request->country,
                        'phone'              => $request->phone,
                        'industry_id'        => $request->industry_id,
                        'entity_description' => $request->entity_description,
                        'created_by'         => Auth::user()->id,
                        'entity_logo'        => $request->entity_logo,
                        'zip_code'           => $request->zip_code,
                        'fax'                => $request->fax,
                    ]);
                } else {
                    $entity = Entity::create([
                        'long_name'          => $request->long_name, 'entity_type_id' => 2,
                        'short_name'         => $request->short_name,
                        'entity_website'     => $request->website,
                        'address1'           => $request->address,
                        'city'               => $request->city,
                        'country'            => $request->country,
                        'phone'              => $request->phone,
                        'email'              => $request->email,
                        'industry_id'        => $request->industry_id,
                        'entity_description' => $request->entity_description,
                        'created_by'         => Auth::user()->id,
                        'entity_logo'        => $request->entity_logo,
                        'zip_code'           => $request->zip_code,
                        'fax'                => $request->fax,
                    ]);
                }

                if ($entity) {
                    if (session()->get('lang') == 'EN')
                        $note = ucfirst($request->type) . ' created on ' . getCreatedAtAttribute(Carbon::now()) . ' by ' . (Auth::user()->fname . ' ' . Auth::user()->lname);
                    else
                        $note = ucfirst($request->type) . ' créée le ' . getCreatedAtAttribute(Carbon::now()) . ' par ' . (Auth::user()->fname . ' ' . Auth::user()->lname);
                    $this->notesService->addNote(['type' => $request->type, 'field_id' => $entity->id, 'notes' => $note]);

                    return response()->json(['status' => TRUE, 'data' => $entity]);
                } else {
                    return response()->json(['status' => FALSE, 'data' => []]);
                }
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'data' => $e->getMessage()]);
            }
        }

        public function checkUnique()
        {
            $hashRand = generateRandomValue(3, 1);
            $checkCodeUnique = DB::connection('mysql')->table('hostname_codes')->get(['hash'])->toArray();
            if (!in_array($hashRand, $checkCodeUnique)) {
                return $hashRand;
            } else {
                $this->checkUnique();
            }
        }

        public function updatePermission(Request $request)
        {
            try {
                if (Auth::user()->role == 'M1') {
                    $updateData = [
                        'role'           => $request->role ?? 'M2',
                        'role_commision' => $request->role_commision ?? 0,
                        'role_wiki'      => $request->role_wiki ?? 0,
                        'permissions'    => $request->permissions,
                    ];
                } else {
                    $updateData = [
                        'permissions' => $request->permissions,
                    ];
                }
                $res = User::where('id', $request->id)->update($updateData);
                if ($res) {
                    $user = User::where('id', $request->id)->first(['permissions', 'role', 'role_commision', 'role_wiki']);
                    return response()->json(['status' => TRUE, 'data' => $user]);
                } else {
                    return response()->json(['status' => FALSE, 'data' => []]);
                }
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'data' => $e->getMessage()]);
            }
        }

        public function getAutoUnions($val)
        {
            $entity = Entity::where('long_name', 'LIKE', $val . '%')->where('entity_type_id', 3)->get(['id', 'long_name']);
            return response()->json($entity);
        }
    }
