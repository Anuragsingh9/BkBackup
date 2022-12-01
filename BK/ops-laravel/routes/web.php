<?php

    use App\LabelCustomization;
    use App\User;
    use App\Union;


    
    
    Route::group(['middleware' => ['web']], function () {
        Route::get('signin', 'UserController@loginForm')->name('signin');
        Route::post('signin-process', 'UserController@loginUser')->name('signin-process');
    });
    Route::get('/single-gen-referrer-pdf/{referrerId}/{refFieldId}/{stepId}', 'PdfController@genReferrerPdfSinge');
//very First Route of App
    Route::get('/', 'UserController@reactApp');
    Route::get('/qualification/change-password', function () {
        return redirect('change-password');
    });
    Route::get('/qualification/preregistration-form', function () {
        return view('carte-pro.page-ask-for-card')->with('account', 'abc');
        //return view('carte-pro.page-home')->with('account', 'avc');
    })->name('carte-pro');
    Route::get('/carte-pro/demandez-votre-carte', function () {
        return view('carte-pro.page-ask-for-card')->with('account', 'abc');
    })->name('demandez-votre-carte');
    Route::get('/referrer/{name}', 'UserController@reactApp')->where('name', '.*');
    Route::get('/op-crm-pour-organisations-professionnelles', 'CoreController@launch');
    Route::post('/send-launching-email', 'CoreController@sendLaunchEmail');

    Route::get('testEmail', 'DashboardController@sendDummyMail');
    Route::get('lists/export', 'Universal\ListImportController@export');
    Route::group(['middleware' => ['cors', 'web'], 'prefix' => '', 'namespace' => ''], function () {
        Route::post('registration-check-code', '\Modules\Qualification\Http\Controllers\RegistrationController@checkCode');
    });
//List Route
    Route::group(['middleware' => 'cors', 'prefix' => '', 'namespace' => 'Universal'], function () {
        Route::post('import-step-2', 'ListImportController@importStepTwo');
        Route::resource('import-lists', 'ListImportController');
        Route::post('lists/person-list', 'ListsController@personList');
        Route::post('lists/addlist', 'ListsController@addList');
        Route::post('lists/add-external-contact-in-list', 'ListsController@addContactToExternalList');
        //getTemplate Download
        Route::get('get-template/{type?}', 'ListImportController@getTemplateDownload');
        Route::get('get-typo', 'ListsController@getTypology');
        Route::get('list-filter/{searchKeyword}', 'ListsController@searchList');
        Route::get('fetch-filterd-contact/{searchKeyword}', 'ListsController@searchContactExternalList');
        Route::resource('lists', 'ListsController', ['as' => 'a']);


    });


    Route::get('/test', function () {
        dd(shell_exec('php artisan route:list'));
        // dd(config('constants.PROJECT'));
        dd(Storage::disk('tenant')->get('readme.md'));
        dd(Storage::disk('tenant')->put('readme.md', 'RRRR.'));
        dd(md5(sprintf(
            '%s.%d',
            'base64:DJSIzuXKr6lAFDfFqPbd3YuhpQBeL2O3sQRXTDROW1E=',
            1
        )));

        $hashRand = generateRandomValue(3, 1);
//    $checkCodeUnique = DB::connection('mysql')->table('hostname_codes')->where('hash', $hashRand)->count();
        $checkCodeUnique = 1;
        $checkDomain = DB::connection('mysql')->table('hostname_codes')->where('fqdn', 'new')->count();
        if ($checkDomain == 0 && $checkCodeUnique == 0) {
            dd('new');
        } else {
            if ($checkDomain == 0 && $checkCodeUnique != 0) {
                dd(['fqdn' => 'ccc', 'hash' => checkUnique()]);
            }
        }
    });

    date_default_timezone_set('Europe/Paris');
// Route::get('/check', function () {
//      $user= DB::connection('mysql')->table('wp_users')->get();
//     $userData=[];
//     foreach ($user as $key => $value) {
//         $userData[]=$value;
//     }
//     dd($userData);
// });
    Route::get('/db', function () {
        dd(bcrypt('laurent.perillon@internetbusinessbooster.com'));
        dd(password_hash('rural@lly', PASSWORD_BCRYPT));
        dd(phpinfo());
        shell_exec('mysqldump --databases op-rest > dump.sql');
    });
//Update family_id and industry_id based on union_id user table
    Route::get('/update-family', function () {
        $user = User::get();
        foreach ($user as $key => $value) {

            if ($value->union_id != NULL && (int)$value->union_id > 0) {

                $union = Union::find($value->union_id);
                if ($union) {
                    $user = User::where('id', $value->id)->update(['family_id' => $union->family_id, 'industry_id' => $union->industry_id]);
                    if ($user) {
                        echo $value->id . '<br/>';
                    }
                }
            }
        }
    });
    Route::get('/remove-tags', function () {

        removeTagsInsideEmailTags();
    });
    Route::get('/settingCheck', function () {
        $settings = getSettingData('decision_push_setting');
        dd(str_replace('<p>&nbsp;</p>', '', $settings->notification_text));
    });
//route for env mode undefined
    Route::get('/mode-error', ['uses' => 'CoreController@displayAlert']);

    Route::get('/mailOnOff', ['uses' => 'CoreController@SendMassEmail']);
    Route::get('/push', ['uses' => 'PushNotificationController@index']);
    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */
    Route::get('app-link/{id}', 'CoreController@appLink');
    Route::get('/sendEmail', 'CronController@sendEmail');
//change password for first time
    Route::get('/user', function () {
        $a = 20;
        $b = &$a;
        $b = 10;
        echo $a . ',' . $b;
        exit;
        $users = User::where('login_count', 0)->get();
        foreach ($users as $user) {
            $user->password = bcrypt($user->email);
            $user->save();
        }
        dd($users);
    });
    Route::get('/move-account', 'UserController@moveAccount');
    Route::get('/testMail', 'MeetingController@testMail');
    Route::get('/testUpload', 'CoreController@getPrivateAsset');
    Route::get('/checkDbEntry', 'UserController@checkDbEntry');
    Route::get('/welcome', function () {


        //    dd($_SERVER);
        //    echo php_uname();
        //    echo PHP_OS;
        //
        //    /* Some possible outputs:
        //    Linux localhost 2.4.21-0.13mdk #1 Fri Mar 14 15:08:06 EST 2003 i686
        //    Linux
        //
        //    FreeBSD localhost 3.2-RELEASE #15: Mon Dec 17 08:46:02 GMT 2001
        //    FreeBSD
        //
        //    Windows NT XN1 5.1 build 2600
        //    WINNT
        //    */
        //
        //    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        //        echo 'This is a server using Windows!';
        //    } else {
        //        echo 'This is a server not using Windows!';
        //    }
        //    exit;

        $hostCode = '494';
        //$hostCode=generateRandomValue(4);
        $randCode = generateRandomValue(3);
        $setPass = setPasscode($hostCode, $randCode);
        $getPass = getPasscode($setPass['hashCode']);
        dd($setPass);
        //    dd(setPasscode($hostCode, $randCode));
        //    dd(getPasscode('109212604'));exit;
        $passCode = 'YAzUHNox';
        $userHash = substr($passCode, 2, 3) . substr($passCode, 6, 1);
        $mainHash = substr($passCode, 0, 2) . substr($passCode, 5, 1) . substr($passCode, 7, 1);
        dd($userHash, $mainHash);
        $userCode = 'YANx';
        //$userCode=generateRandomValue(4);
        $userCode1 = 'zUHo';
        //$userCode1=generateRandomValue(4);
        dump($userCode, $userCode1);
        $userCode2 = substr($userCode, 0, 2) . substr($userCode1, 0, 3) . substr($userCode, 2, 1) . substr($userCode1, 3, 1) . substr($userCode, 3, 1);
        dd($userCode2);
        dd(md5('BXET$1%V92'), mc_encrypt('79b787728a5e0a3da208977532637348_ram'), mc_decrypt('Y3BhWEpKeExESWhXK0dlQTFEbndSbG9TQ2NnQytaUjB0UlZxbTdkRUdNSS80UmUrY01YV2RiSDU2UmFVQnJLNw'));
        dd(json_encode($data = [
            ['name' => 'doodle', 'is_show' => 1, 'order' => 1],
            ['name' => 'task', 'is_show' => 1, 'order' => 2],
            ['name' => 'commissions', 'is_show' => 1, 'order' => 3],
            ['name' => 'calendar', 'is_show' => 1, 'order' => 4],
            ['name' => 'document', 'is_show' => 1, 'order' => 5],
            ['name' => 'search', 'is_show' => 1, 'order' => 6],
        ]));
        return view('welcome');
    });
    Route::get('/style.css', 'StyleController@style');
    Route::get('/survey-style.css', 'StyleController@surveyStyle');
    Route::get('/newsletter-style.css', 'StyleController@newsletterStyle');
    Route::get('/crm-style.css', 'StyleController@crmStyle');
    Route::get('/qualification-style.css', 'StyleController@qualificationStyle');
    /*end repd offline*/
    /**/
    Route::post('/updatetemplate/{id}/{accId}', 'SuperAdminController@updateTemplate')->name('updatetemplate');
    Route::get('upload-template-setting/{id}', 'SuperAdminController@uploadTemplate')->name('upload-template-setting');
    Route::get('add-template/{id}', 'SuperAdminController@addTemplate')->name('add-template');
    Route::get('edit-template/{id}/{accId}', 'SuperAdminController@editTemplate')->name('edit-template');
    Route::post('/savetemplate/{id}', 'SuperAdminController@saveTemplate')->name('savetemplate');
    Route::get('/deletetemplate/{id}/{accId}', 'SuperAdminController@deleteTemplate')->name('deletetemplate');
    /**/
    Route::get('/adminList', 'SuperAdminController@adminList')->name('adminList');
    Route::get('/newsuperadmin/{id?}', 'SuperAdminController@newsuperadmin')->name('newsuperadmin');
    Route::get('/deltesuperadmin/{id?}', 'SuperAdminController@deltesuperadmin')->name('deltesuperadmin');
    Route::get('/accounts', 'SuperAdminController@accounts');
    Route::post('/do-signin-process', 'SuperAdminController@doSigninProcess')->name('do-signin-process');
    Route::post('/savesuperadmin', 'SuperAdminController@savesuperadmin')->name('savesuperadmin');
    Route::get('/settings/{id}', 'SuperAdminController@settings')->name('settings');
    Route::get('/access/{id}', 'SuperAdminController@access')->name('access');
    Route::post('/do-set-settings', 'SuperAdminController@doSetSettings')->name('do-set-settings');
    Route::get('/super-admin-login', 'SuperAdminController@signin');
    Route::get('guide-list', 'SuperAdminController@superAdminGuide')->name('guide-list');
    Route::post('upload-guide-list', 'SuperAdminController@guidePdfUpload')->name('upload-guide-list');
    Route::post('upload-grdp', 'SuperAdminController@uploadGRDP')->name('upload-grdp');
    Route::post('upload-project', 'SuperAdminController@uploadProject')->name('upload-project');
    Route::get('sync-bluejeans-users', 'SuperAdminController@syncBlueJeansUser')->name('sync-bluejeans-users');
    Route::post('add-superadmin-hostname-details','SuperAdminController@storeSuperAdminHostname');
    Route::get('bulk-acc/create', 'SuperAdminController@viewBulkCreation')->name('bulk-acc-creation');
    Route::post('bulk-acc/store', 'SuperAdminController@storeOrgAcc');
    Route::get('bulk-acc/super-staff-access/{id}', 'SuperAdminController@staffLoginAccess')->name('bulk-staff-access');
    Route::get('bulk-acc/redirect-access/{email}/{token}', 'SuperAdminController@bulkAccRedirectApp');


//modules-routes
    Route::get('add-module-list', 'SuperAdminModuleController@create')->name('add-module-list')->middleware('SuperAdmin');
    Route::get('module-list', 'SuperAdminModuleController@index')->name('moduleList')->middleware('SuperAdmin');
    Route::post('upload-module-list', 'SuperAdminModuleController@store')->name('upload-module')->middleware('SuperAdmin');
    Route::get('/edit-module/{id}', 'SuperAdminModuleController@edit')->name('edit-module')->middleware('SuperAdmin');
    Route::get('/deltemodule/{id?}', 'SuperAdminModuleController@destroy')->name('deltemodule')->middleware('SuperAdmin');
    Route::post('/module/update-order', 'SuperAdminModuleController@updateOrder')->name('moduleOrderUpdate')->middleware('SuperAdmin');

//==========================//

// Tracking
    Route::get('/adobe-stock-tracking', 'SuperAdminController@adobeStockTracking')->name('adobe-stock-tracking');
    Route::get('/transcribe-logs', 'SuperAdminController@transcribeTracking')->name('transcribe-tracking');

    Route::post('upload-project-template', 'SuperAdminController@uploadProjectTemplate')->name('upload-project-template');

    Route::get('delete-guide/{lang}/{id}', 'SuperAdminController@deleteGuide')->name('delete-guide');
    Route::get('delete-grdp/{lang}/{id}', 'SuperAdminController@deleteGrdp')->name('delete-grdp');
//lang
    Route::post('/change-language', 'InitController@languageChange')->name('change-lang');

    Route::get('/logout/', 'SuperAdminController@logout')->name('savesuperadminlogout');
    Route::get('/logoutt/', 'StaffAuthController@logout')->name('stafflogout');
    Route::get('/app-login/{token}', 'SuperAdminController@loginApp')->name('super-admin-login-app');

//staff login
    Route::get('/support-staff-login', 'StaffAuthController@signin');
    Route::post('/support-staff-login', 'StaffAuthController@postSignin')->name('staff-signin');
    Route::get('/get-account', 'StaffAuthController@accounts');
    Route::post('/get-account', 'StaffAuthController@postAccounts')->name('get-account');
    Route::get('/staffList', 'StaffAuthController@staffList')->name('staffList');
    Route::get('/newstafflogin/{id?}', 'StaffAuthController@newstafflogin')->name('newstafflogin');
    Route::get('/deltestaff/{id?}', 'StaffAuthController@deltestaff')->name('deltestaff');


    Route::post('/savesuperstaff', 'StaffAuthController@savesuperstaff')->name('savesuperstaff');
    Route::get('/logoutt/', 'StaffAuthController@logout')->name('stafflogout');


// Route::get('/app-mem/','WorkshopController@add30Member');
//for aimcc construction login
    Route::get('aimcc-login', 'UserController@aimccLoginForm');
    Route::post('submit-aimcc-login', 'UserController@submitAimccLoginForm')->name('aimcc-signin-process');
    Route::get('/aimcc-forgot-password', 'UserController@aimccForgetPassword')->name('aimcc-forgot-password');

//for adn construction login
    Route::get('adn-login', 'UserController@adnLoginForm');

    Route::get('cartepro-login', 'UserController@carteProLoginForm');
    Route::post('submit-cartepro-login', 'UserController@submitCarteProLoginForm')->name('cartepro-signin-process');


    Route::post('submit-adn-login', 'UserController@submitAdnLoginForm')->name('adn-signin-process');
    Route::get('/adn-forgot-password', 'UserController@adnForgetPassword')->name('adn-forgot-password');
    Route::get('signout', 'UserController@signOut')->name('signout');
    Route::get('signout_guest', 'UserController@signoutGuest')->name('signout_guest');
    Route::get('change-password', 'UserController@changePasswordForm')->name('change-password');
    Route::post('change-password-process', 'UserController@changePasswordProcess')->name('change-password-process');

    Route::get('signup', 'UserController@signupEmailForm')->name('signup-email-form');
    Route::post('signup-email', 'UserController@signupEmail')->name('signup-email');
    Route::get('signup/waiting_for_confirm', 'UserController@signupVerification')->name('signup-verification');
    Route::get('signup-steps', 'UserController@signupSteps')->name('signup-steps');

    Route::get('/forgot-password', 'UserController@forgetPassView')->name('forgot-password');
    Route::post('/forgot-password', 'UserController@forgetPassword')->name('forgot-password-process');

    Route::get('/reset-password', 'UserController@resetPassView')->name('reset-password');
    Route::post('/reset-password', 'UserController@resetPassword')->name('reset-password-process');

    Route::get('/meeting-view', 'UserController@redirectMeetingView')->name('redirect-meeting-view');
    Route::get('/redirect-url', 'UserController@redirectAppUrl')->name('redirect-app-url');
    Route::get('/guest-meeting-view/{token?}', 'UserController@redirectGuestMeetingView')->name('guest-meeting-view');
    Route::get('/prepd-pdf-view/{mid}/{wid}', 'PdfController@prepdPdfView');
    Route::get('/repd-pdf-view/{mid}/{wid}', 'PdfController@repdPdfView');

    Route::get('/gen-repd-pdf/{mid}/{wid}/{lang?}', 'PdfController@genRepdPdf');
    Route::get('/download-static-prped-pdf/{mid}/{wid}/{type}', 'DocumentController@downloadPrepdStaticPdf');
    Route::get('/download-inscription-pdf/{mid}/{wid}', 'PdfController@downloadInscriptionPdf');
    Route::get('/inscription-pdf-view/{mid}/{wid}/{lang?}', 'PdfController@genInscriptionPdf');
    Route::get('/download-signature/{wid}/{mid}/', 'DocumentController@genSignaturePdf');
    Route::get('/download-guide/{lang}/{id}/', 'SuperAdminController@downloadGuide');
    Route::get('/download-grdp/{lang}/{id}/', 'SuperAdminController@downloadGrdp');
    Route::get('/gen-prepd-pdf/{mid}/{wid}/{lang?}', 'PdfController@genPrepdPdf');
//this for referrer response
    Route::get('/gen-referrer-pdf/{referrerId}/{refFieldId}', 'PdfController@genReferrerPdf');

    Route::get('/prepd-footer/{mid}/{wid}', 'CoreController@prepdFooter');
    Route::get('/repd-footer/{mid}/{wid}', 'CoreController@repdFooter');
    Route::get('/do-reminder', 'CronController@reminder');
    Route::get('/do-final-meeting/{id?}', 'CronController@doFinalPREPD');
    Route::get('/get-zip-download/{wid}', 'PdfController@zipData');

    Auth::routes();
    Route::group(['prefix' => 'seeds'], function () {
        Route::get('gen-seeds/{table}', 'CoreController@generateSeeds');
    });
    Route::group(['middleware' => ['auth', 'cors', 'action_log']], function () {
        Route::get('home', 'UserController@home');
        //digital presense
        Route::get('/redirect-video-meeting/{id}', 'MeetingController@digitalMeetingPresense');
        Route::get('/download-document', 'DocumentController@downloadDocument');
        Route::get('/download-offline-software', 'DocumentController@downloadOfflineSoftware');
        Route::get('/download-resource', 'DocumentController@downloadResource');
        Route::get('/update-task-status', 'CronController@updateTaskStatus');
        //import sample download route
        Route::get('/industry-sample', 'Import\ExcelTemplateController@excelIndustrySample');
        Route::get('/import-sample/{type}', 'Import\ExcelTemplateController@generateExcelSample');
    });

    Route::get('messageCategory', function () {
        $messageCategory = messageCategory::pluck('workshop_id');
        $workshops = Workshop::WhereNotIn('id', $messageCategory);
        foreach ($workshops as $value) {
            dump([
                'category_name' => 'General',
                'workshop_id'   => $value->id,
            ]);
            // messageCategory::create([
            //     'category_name'=>'General',
            //     'workshop_id'=>$value->id
            // ]);
        }
    });
//List Route

    Route::group(['middleware' => 'cors', 'prefix' => '', 'namespace' => 'Universal'], function () {

        Route::post('lists/person-list', 'ListsController@personList');
        Route::post('lists/addlist', 'ListsController@addList');
        Route::post('lists/removelist', 'ListsController@removeUserList');
        Route::get('lists/get-stats/{listId}', 'ListsController@getStats');
        Route::get('get-typo', 'ListsController@getTypology');
        Route::get('list-filter/{searchKeyword}', 'ListsController@searchList');
        Route::get('fetch-filterd-contact/{searchKeyword}', 'ListsController@searchContactExternalList');
        Route::get('fetch-filterd-all/{searchKeyword}', 'ListsController@searchAllcontact');
        Route::get('fetch-contact/{id}', 'ListsController@getContact');
        Route::resource('lists', 'ListsController');

        Route::post('import-step-2', 'ListImportController@importStepTwo');
        Route::resource('import-lists', 'ListImportController');
    });

    Route::get('/weekly_reminder', function () {
        $hostnames = DB::table('hostnames')->get();
        foreach ($hostnames as $key => $value) {
            DB::table('weekly_reminders')->insert(
                ['fqdn' => $value->fqdn, 'status' => 0]
            );
        }
    });
    Route::get('/weekly_reminder_view', function () {
        $data = [
            [
                'name'       => 'Syndicat',
                'default_en' => 'Family of industries',
                'default_fr' => 'Famille d’industries',

            ], [
                'name'       => 'Société',
                'default_en' => 'Industries',
                'default_fr' => 'Industries',

            ], [
                'name'       => 'Union',
                'default_en' => 'Union',
                'default_fr' => 'Syndicat',
            ], [
                'name'       => 'Position',
                'default_en' => 'Position in the union',
                'default_fr' => 'Fonction dans le syndicat ',
            ], [
                'name'       => 'Company',
                'default_en' => 'Company',
                'default_fr' => 'Société',
            ], [
                'name'       => 'Pos_Company',
                'default_en' => 'Position in the company',
                'default_fr' => 'Fonction dans la société',
            ],
        ];
        foreach ($data as $key => $value) {

            LabelCustomization::updateOrCreate(['name' => $value['name']], $value);
        }
        return view('email_template.weekly_reminder')->with([
            'doodle_workshop' => ['workshop' => [], 'workshop_doodle' => []],
            'task'            => [],
            'docs'            => [],
            'user'            => [],
        ]);
    });
    Route::get('/getHostdata', 'CronController@getHostdata');
    Route::get('/getEmailData', 'CronController@getEmailData');
    Route::get('/run-migration/{id}/{seed?}', 'CronController@migrationScript');
    Route::get('/run-seeder/{id}/{class}', 'CronController@migrationSeederScript');
    Route::get('/run-module-seeders/{id}/{module}', 'CronController@migrationSeederForModule');
    Route::get('/run-module-seeder/{module}/{class}', 'CronController@migrationModuleSeederScript')->middleware('auth');
    Route::get('migrate-union/{id?}','CronController@unionToEntityMigration')->middleware('auth');
    Route::get('/run-nsl-script/{id}', 'CronController@deleteNSL');
    Route::get('/run-pass-code/{id}', 'CronController@scriptForPassCode');
    Route::get('addProject', 'CoreController@addOldWorkshopDefaultTask');
    Route::get('/updateWeekly', 'CronController@updateWeeklyReminderTable');

//apiKey generate
    Route::get('/gen-api/{id}', 'CoreController@createAPiKey');

//cron url for final prepd added 09/10/2018 by vijay
    Route::get('/update-prepd-final', 'CronController@doFinalPREPD');
    Route::get('/do-final-meeting/{id?}', 'CronController@doFinalPREPD');
    Route::get('update-start-date', 'CoreController@updateMilestoneStartDate');
    Route::get('update-task-status', 'CoreController@updateTaskStatus');


//ajax request
    Route::get('search', 'StaffAuthController@search');
    Route::post('search-user', 'StaffAuthController@searchUser');
    Route::get('/{token}', 'StaffAuthController@getTokenLogin');

    Route::get('test-email-new/{id}', 'CoreController@testMailNew');

