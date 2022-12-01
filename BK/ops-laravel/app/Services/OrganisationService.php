<?php


namespace App\Services;


use Artisan;
use Hash;
use App\User;
use App\BulkAccAdmin;
use App\Exceptions\CustomException;
use App\Exceptions\CustomValidationException;
use App\Http\Controllers\CoreController;
use App\Issuer;
use App\Organisation;
use App\Setting;
use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrganisationService extends Service {
    
    /**
     * @var WebsiteRepository
     */
    private $websiteRepository;
    /**
     * @var HostnameRepository
     */
    private $hostnameRepository;
    /**
     * @var Environment
     */
    private $tenancy;
    /**
     * @var CoreController
     */
    private $coreController;
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the website repository class from the tenant package
     * This will check in current class variable for the website repository object,
     * if not present fill in it
     * and in end it will return that class variable so it will like singleton to that
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return WebsiteRepository
     */
    public function getWebsiteRepository() {
        if (!$this->websiteRepository) {
            $this->websiteRepository = app(WebsiteRepository::class);
        }
        return $this->websiteRepository;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the hostname repository object from tenant
     * This will check in current class variable for the hostname repository object,
     * if not present fill in it
     * and in end it will return that class variable so it will like singleton to that
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return HostnameRepository
     */
    public function getHostnameRepository() {
        if (!$this->hostnameRepository) {
            $this->hostnameRepository = app(HostnameRepository::class);
        }
        return $this->hostnameRepository;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To return the tenancy environment from the Hyn Tenancy
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return Environment
     */
    public function getTenancy() {
        if (!$this->tenancy) {
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);
        }
        return $this->tenancy;
    }
    
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to get the core controller
     * as many of the service methods are defined in core controller from previously so using the core controller here
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return CoreController
     */
    public function getCoreController() {
        if (!$this->coreController) {
            $this->coreController = app(\App\Http\Controllers\CoreController::class);
        }
        return $this->coreController;
    }
    
    
    public function addSuperAdminHostname($request) {
        $checkHostname = Hostname::where('hostname_id', $request->hostname_id);
        if (!$checkHostname) {
            return BulkAccAdmin::create(['hostname_id' => $request->hostname_id, 'super_admin_id' => Auth::user()->id]);
        } else {
            throw new CustomValidationException('Hostname already exists');
        }
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will create a Organisation account with the provided details
     * @algorithm
     * - this will first prepare the data for organisation
     *      -    in preparation it will create the website and hostname this will create a database with core migrations
     * - create organisation in main db
     * - create hostname code in main db
     * - weekly reminder entry in main db
     * - change connection to the new account database
     * - create S3 bucket.
     * then after it will perform the actions inside the new account database
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $orgFname // first name of the organiser of the account
     * @param $orgLname // last name of the organiser of the account
     * @param $orgEmail // email of organiser
     * @param $orgName // name of the organisation
     * @param $accName // name of the account
     * @param int $evKct // to indicate the event and kct will be enabled or not, to be used in future
     * @return Hostname
     * @throws CustomException
     * @throws CustomValidationException
     */
    public function createAccount($orgFname, $orgLname, $orgEmail, $orgName, $accName, $evKct = 0) {
        // storing current hostname as after creating website it will be destroyed
        $data = $this->prepareDataForAccountCreate($orgFname, $orgLname, $orgEmail, $orgName, $accName);
        // setting the hostname which was in starting
        $this->createOrganisation($data['organisation'], 'mysql');
        $this->createHostnameCode($data["hostname"], $data["hostname_hash"]);
        $this->enableWeeklyReminder($data["hostname"]);
        $this->changeTenantConnection($data["hostname"]);
        $this->createS3Directory($data["domain"]);
        // here hostname has changed so new created account database tables will be selected by tenant not main db
        $organisation = $this->createOrganisation($data['organisation']);
        $this->createIssuer($data['organisation']['name_org'], $data['organisation']['acronym']);
        // organisation of account level table is passed
        $this->setSettings($data);
        $this->setDefaultAccountSettings($data["hostname"]->id);
        $this->createOrgAdmin($data['orgAdmin']);
        $this->createOrgAdmin($data['staffAdmin']);
        $this->runArtisanCommandsForNewAccount();
        
        $this->sendAccountCreateMail($data['organisation']['email'], $data['organisation']['fname'], $data['domain_path']);
        return $data['hostname'];
    }
    
    /**
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description to prepare the data for creating the account from
     * This will prepare the data and put the defaults value for the fields which are not getting from user
     * like short name etc. according to the requirements.
     * @note This will create a entry in website and hostnames table also
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $orgFname
     * @param $orgLname
     * @param $orgEmail
     * @param $orgName
     * @param $accName
     * @return array
     * @throws CustomValidationException
     */
    public function prepareDataForAccountCreate($orgFname, $orgLname, $orgEmail, $orgName, $accName) {
        $hostnameHash = $this->createHostnameHash();
        $domainPath = $accName . '.' . env('HOST_SUFFIX');
        $website = $this->createWebSite();
        $hostname = $this->createHostname($domainPath, $website);
        $codeForOrg = setPasscode($hostnameHash, generateRandomValue(3));
        $codeForSupport = setPasscode($hostnameHash, generateRandomValue(3));
        $permissions = "{\"crmAdmin\":0,\"crmEditor\":0,\"crmAssistance\":0,\"crmRecruitment\":0}";
        return [
            // account_info
            "domain"        => $accName,
            "domain_path"   => $domainPath,
            "website"       => $website,
            "hostname"      => $hostname,
//            "website"       => 'gourav',
//            "hostname"      => 'gourav',
            "hostname_hash" => $hostnameHash,
            // organisation data
            
            "organisation" => [
                // personal info
                'account_id'       => $hostname->id,
//                'account_id'       => 100,
                'fname'            => $orgFname,
                'lname'            => $orgLname,
                'password'         => Hash::make($orgEmail), // email is password for first time
                'email'            => $orgEmail,
                // organisation info
                'name_org'         => $orgName,
                'acronym'          => strtoupper($accName),
                'sector'           => null,
                'members_count'    => 999999,
                'permanent_member' => 999999,
                'commissions'      => 999999,
                'working_groups'   => 999999,
                'address1'         => config('constants.defaults.organisation.address1'),
                'address2'         => config('constants.defaults.organisation.address2'),
                'postal_code'      => config('constants.defaults.organisation.postal_code'),
                'city'             => config('constants.defaults.organisation.city'),
                'country'          => config('constants.defaults.organisation.country'),
            ],
            'orgAdmin'     => [
                'fname'          => $orgFname,
                'lname'          => $orgLname,
                'password'       => Hash::make($orgEmail), // email is password for first time
                'email'          => $orgEmail,
                'role'           => 'M1',
                'role_commision' => 1,
                'role_wiki'      => 1,
                'login_count'    => 1,
                'login_code'     => $codeForOrg['userCode'],
                'hash_code'      => $codeForOrg['hashCode'],
                'permissions'    => $permissions,
            ],
            'staffAdmin'   => [
                'fname'          => 'Support',
                'lname'          => 'Staff',
                'password'       => Hash::make(session()->get('superadmin')->email), // email is password for first time
                'email'          => session()->get('superadmin')->email,
                'role'           => 'M1',
                'role_commision' => 1,
                'role_wiki'      => 1,
                'login_count'    => 1,
                'login_code'     => $codeForSupport['userCode'],
                'hash_code'      => $codeForSupport['hashCode'],
                'permissions'    => $permissions,
            ]
        ];
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description this method will prepare the unique hostname hash
     * first it will get all the hostname hashes
     * then it will check count of hostname hashes must less than possible by digits available in hostname hashes
     * then it will recursively prepare the hash code which is not present in existing
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @throws CustomValidationException
     */
    public function createHostnameHash() {
        $existingCodes = DB::connection('mysql')->table('hostname_codes')->get([DB::raw('DISTINCT(hash)')])->pluck('hash')->toArray();
        // this will ensure the hostname hash code is possible to create
        // so infinite recursion can't happen
        $this->checkIsHashesPossible($existingCodes);
        return $this->checkRecHostnameHash($existingCodes);
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will check the is hash possible to generate or not
     * It will first check the count of the current hashes created,
     * this count must be less than possible hashes
     *
     * @note Here possible hashes means if hash length is 3 then there can be 1000 (if count 000) hashes at max
     * but from previous functionality for the hash code we are using only 1,2,3,4 in hash code digit
     * so there can be only
     * 4 * 4 * 4
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param array $codes
     * @param string $digits
     * @throws CustomValidationException
     */
    public function checkIsHashesPossible($codes, $digits = '123456789') {
        $maxLength = config('constants.hostname_code_length');
        $currentCodesCreated = count($codes);
        // as if 3 length code possible with 9 digit then there can be max, 9 * 9 * 9 = 729 codes possible
        $maxCodePossible = pow(strlen($digits), $maxLength);
        
        if ($currentCodesCreated >= $maxCodePossible) {
            // there are already codes created as possible
            throw new CustomValidationException(__('out_of_hostname_hash'));
        }
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description this method will check and prepare the hash code for the
     * @warn @recursion is present
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $code
     * @param $excludes
     * @return string
     */
    public function checkRecHostnameHash(&$excludes, $code = null) {
        if ($code) {
            if (in_array($code, $excludes)) {
                $code = generateRandomValue(config('constants.hostname_code_length'), null, "123456789");
                return $this->checkRecHostnameHash($excludes, $code);
            }
            return $code;
        } else {
            $code = generateRandomValue(config('constants.hostname_code_length'), null, "123456789");
            return $this->checkRecHostnameHash($excludes, $code);
        }
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create a website object for the tenant
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return Website
     */
    public function createWebSite() {
        $website = new Website;
        $this->getWebsiteRepository()->create($website);
        return $website;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create a tenant hostname model which contains the hostname details.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $domainPath
     * @param $website
     * @return Hostname
     */
    public function createHostname($domainPath, &$website) {
        $hostname = new Hostname;
        $hostname->fqdn = $domainPath;
        $this->getHostnameRepository()->attach($hostname, $website);
        return $hostname;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create the organisation with the parameters passed
     * this will throw error if organisation not created
     * @note the id of organisation will return only
     * @warn after website (tenant) is created it flush the database connection cache,
     * so use the DB::connection manually everytime only
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $data
     * @return Organisation
     * @throws CustomException
     */
    public function createOrganisation($data, $connection = null) {
        if ($connection == 'mysql') {
            $organisation = DB::connection('mysql')->table('organisation')->insert($data);
            if (!$organisation) {
                throw new CustomException("Error in creating main organisation", 500);
            }
            return DB::connection('mysql')->table('organisation')->find($organisation);
        } else {
            $organisationId = Organisation::insertGetId($data);
            if (!$organisationId) {
                throw new CustomException("Error in creating organisation", 500);
            }
            return Organisation::find($organisationId);
        }
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create a hostname code for the hostname
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Hostname $hostname
     * @param $hash
     * @return boolean
     * @throws CustomException
     */
    public function createHostnameCode($hostname, $hash) {
        $hostnameCode = DB::connection('mysql')->table('hostname_codes')->insert(['fqdn' => $hostname->fqdn, 'hash' => $hash]);
        if (!$hostnameCode) {
            throw new CustomException("Error in creating hostname code", 500);
        }
        return $hostnameCode;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To enable the weekly reminders for the account by creating a entry in weekly_reminders table
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Hostname $hostname
     * @return boolean
     * @throws CustomException
     */
    public function enableWeeklyReminder($hostname) {
        $addWeekly = DB::connection('mysql')->table('weekly_reminders')
            ->insert(['fqdn' => $hostname->fqdn, 'status' => 0, 'on_off' => 1]);
        if (!$addWeekly) {
            throw new CustomException("Error in enabling weekly reminders", 500);
        }
        return $addWeekly;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to change the current hostname connection to the provided hostname
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Hostname|\Hyn\Tenancy\Contracts\Hostname $hostname
     * @return Hostname
     */
    public function changeTenantConnection($hostname) {
        $this->getTenancy()->hostname($hostname);
        $this->getTenancy()->identifyHostname();
        return $hostname;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create the s3 directory/folder.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $directory
     */
    public function createS3Directory($directory) {
        $this->getCoreController()->makeDirectoryS3($directory);
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create a new issuer for the new account
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $orgName
     * @param $shortName
     * @return mixed
     * @throws CustomException
     */
    public function createIssuer($orgName, $shortName) {
        $issuer = Issuer::insert(['issuer_name' => $orgName, 'issuer_code' => strtoupper($shortName)]);
        if (!$issuer) {
            throw new CustomException("Error in creating issuer", 500);
        }
        return $issuer;
    }
    
    public function setSettings($data) {
        $this->setEmailGraphics($data['organisation']['fname'], $data['organisation']['lname'], $data['organisation']['email']);
        $this->setPdfGraphics();
        $this->setBluejeansMeetingSetting();
        $this->setLanguageSetting();
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This will set the email graphics for the current hostname account
     * - fetch the email_graphics setting
     * - decode
     * - in email_sign value add the data
     * - encode
     * - save the setting
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $fname
     * @param $lname
     * @param $email
     * @return Setting
     */
    public function setEmailGraphics($fname, $lname, $email) {
        $setting = Setting::where('setting_key', 'email_graphic')->first();
        $json_decode = json_decode($setting->setting_value);
        $json_decode->email_sign = "$fname $lname <br />$email";
        $setting->setting_value = json_encode($json_decode);
        $setting->save();
        return $setting;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To set the pdf graphics setting for the new account
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function setPdfGraphics() {
        //changing pdfGraphic Setting
        $pre_data = getSettingData('graphic_config', 1);
        $postData = [
            'color1'       => ($pre_data->color1),
            'color2'       => ($pre_data->color2),
            'header_logo'  => $pre_data->header_logo,
            'footer_line1' => '',
            'footer_line2' => ''
        ];
        $setting1 = Setting::where('setting_key', 'pdf_graphic')->first();
        $setting1->setting_value = json_encode($postData);
        $setting1->save();
        return $setting1;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To set the default bluejeans account setting for the video meetings
     * This will fetch the details from the env
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function setBluejeansMeetingSetting() {
        $settingValues['client_id'] = env('BLUEJEANS_DEFAULT_ID');
        $settingValues['client_secret'] = env('BLUEJEANS_DEFAULT_SECRET');
        $settingValues['number_of_license'] = 1;
        $data = [
            'setting_key'   => 'video_meeting_api_setting',
            'setting_value' => json_encode($settingValues),
        ];
        $key = 'video_meeting_api_setting';
        return Setting::updateOrCreate(['setting_key' => $key], $data);
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To set the default language for the bulk account creation.
     * This will store the default enabled language to settings table with EN and FR enabled
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function setLanguageSetting() {
        $data = [
            'setting_key'   => 'languages_to_show',
            'setting_value' => json_encode(["EN", "FR"]),
        ];
        $key = 'languages_to_show';
        return Setting::updateOrCreate(['setting_key' => $key], $data);
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description this will set the default account settings for the bulk acc creation , for the provided acc id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $accountId
     * @return mixed
     * @throws CustomException
     */
    public function setDefaultAccountSettings($accountId) {
        $data = AccountSettingService::getInstance()->prepareAccSettingForBulk($accountId);
        $accountSetting = DB::connection('mysql')->table('account_settings')->insert($data);
        if (!$accountSetting) {
            throw new CustomException("Error in setting account setting", 500);
        }
        return $accountSetting;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create a user for the account.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userData
     * @return mixed
     * @throws CustomException
     */
    public function createOrgAdmin($userData) {
        $user = User::insertGetId($userData);
        if (!$user) {
            throw new CustomException("Error in setting account setting", 500);
        }
        return $user;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This will run the required commands for the new account which are related to artisan commands
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function runArtisanCommandsForNewAccount() {
        Artisan::call('migrate', ['--database' => 'tenant']);
        Artisan::call('db:seed', ['--database' => 'tenant']);
        Artisan::call('module:seed', ['--database' => 'tenant']);
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare a access link for the staff user to redirect as staff logged in
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $token
     * @param $email
     * @return string
     * @throws CustomException
     */
    public function prepareStaffAccessLink($token, $email) {
        $accessKeyData = DB::connection('mysql')->table('account_access_keys')->where('access_token', $token)->first();
        if ($accessKeyData) {
            $user = User::where('email', $email)->first();
            if($user) {
                $this->getTenancy()->website();
                $hostname = $this->getTenancy()->hostname();
                Auth::loginUsingId($user->id);
                return env('HOST_TYPE') . $hostname->fqdn . '/#/' . 'dashboard';
            } else {
                throw new CustomException("User not found");
            }
        }
        throw new CustomException('Unauthorized !.', 401);
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To send the mail when account is successfully created
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $email
     * @param $fname
     * @param $domainPath
     */
    public function sendAccountCreateMail($email, $fname , $domainPath) {
        $mailData['mail'] = [
            'subject' => 'Enregistrement terminé',
            'email' => $email,
            'firstname' => 'BHEEM',
            'user' => $fname,
            'organization' => session()->get('organisation_info')['name_org'],
            'path' => env('HOST_TYPE') . $domainPath,
        ];
        $this->getCoreController()->SendEmail($mailData, 'register_email');
    }
}


