<?php

    /**
     * Created by PhpStorm.
     * User: Sourabh Pancharia
     * Date: 7/29/2020
     * Time: 02:45 PM
     */

    namespace Modules\Resilience\Services;


    use App\Entity;
    use App\EntityUser;
    use App\Http\Controllers\CoreController;
    use App\Services\UserService;
    use App\Setting;
    use App\Signup;
    use App\User;
    use App\Workshop;
    use App\WorkshopMeta;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Hash;
    use Modules\Resilience\Entities\Consultation;
    use Modules\Resilience\Entities\ConsultationQuestion;
    use Modules\Resilience\Entities\ConsultationAnswer;
    use Modules\Resilience\Entities\ConsultationAnswerUser;
    use Modules\Resilience\Entities\ConsultationSignUpClassPosition;
    use Modules\Resilience\Entities\ConsultationStep;
    use Modules\Resilience\Entities\ConsultationStepMeeting;
    use Modules\Resilience\Events\LateParticipants;
    use DB;
    use Modules\Resilience\Events\OptionMail;
    use Ramsey\Uuid\Uuid;

    /**
     * Class ResilienceService
     * @package Modules\Resilience\Services
     */
    class ResilienceService
    {

        /**
         * Make instance of ResilienceService singleton class
         * @return ResilienceService|null
         */
        public static function getInstance()
        {
            static $instance = NULL;
            if (NULL === $instance) {
                $instance = new static();
            }
            return $instance;
        }

        /**
         * @param $request
         * @return array|null
         * this function is used to send late participants mails
         */
        public function sendRegisterEmail($request)
        {
            if ($request->workshop_id) {
                $tenancy = app(\Hyn\Tenancy\Environment::class);
                $hostname = $tenancy->hostname()['fqdn'];
                $reminder = Setting::where('setting_key', "consultation_reminder")->first();
                if (isset($reminder->id)) {
                    $reminders = json_decode($reminder->setting_value);
                    if ($reminders->late_participants) {
                        $workshop = Workshop::withoutGlobalScopes()->find($request->workshop_id);
                        $consultation = Consultation::with('workshop')->whereDate('start_date', '<=', Carbon::today()->format('Y-m-d'))->whereDate('end_date', '>=', Carbon::today()->format('Y-m-d'))->where('workshop_id', $request->workshop_id)->orWhere(function ($a) use ($workshop) {
                            if (!$workshop->is_dependent) {
                                $a->orWhereIn('workshop_id', function ($query) use ($workshop) {
                                    $query->select('workshops.id')
                                        ->from('workshops')
                                        ->where(['workshop_type' => 1, 'is_dependent' => 1, 'code1' => $workshop->code1]);
                                });
                            }
                        });
                        if ($consultation->count()) {
                            $this->arrangeMailData($request, $consultation, $hostname);
                        }
                    }
                }
            }
        }

        /**
         * @param $participant
         * @param $workshopId
         * @param $consultation
         * @param $hostname
         * @param $key
         * @return array
         * this function is used to Prepare Email Data with some default tags
         */
        public
        function prepareEmailData($participant, $workshopId, $key, $consultation, $hostname, $questionData = [])
        {
            $workshopData = Workshop::with('parentWorkshop')->find($workshopId);
            $wsetting = getWorkshopSettingData($workshopId);
            $WorkshopSignatory = getWorkshopSignatoryData($workshopId);
            $currUserFname = isset(Auth::user()->fname) ? Auth::user()->fname : '';
            $currUserLname = isset(Auth::user()->lname) ? Auth::user()->lname : '';
            $currUserEmail = isset(Auth::user()->email) ? Auth::user()->email : '';
            $getOrgDetail = getOrgDetail();
            $setting = $this->getSetting($key);
            $member = workshopValidatorPresident($workshopData);
            $mailData = [];
            $preFirstName = !empty($member) ? $member['p']['fname'] : '';
            $preLastName = !empty($member) ? $member['p']['lname'] : '';
            $preEmail = !empty($member) ? $member['p']['email'] : '';
            //this function is for new tags
            $this->newTags($consultation, $workshopData, $mailData, $participant, $hostname, $questionData);
            $mailData['mail']['acc_long_name'] = isset($getOrgDetail->name_org) ? $getOrgDetail->name_org : '';
            $mailData['mail']['acc_short_name'] = isset($getOrgDetail->acronym) ? $getOrgDetail->acronym : '';
            $keywords = [

                '[[ParticipantFN]]'              => (($participant) ? $participant->fname : ''),
                '[[RecipientFirstName]]'         => (($participant) ? $participant->fname : ''),
                '[[RecipientFirstname]]'         => (($participant) ? $participant->fname : ''),
                '[[ParticipantLN]]'              => (($participant) ? $participant->lname : ''),
                '[[RecipientLastName]]'          => (($participant) ? $participant->lname : ''),
                '[[Recipient LastName]]'         => (($participant) ? $participant->lname : ''),
                '[[WorkshopShortName]]'          => $workshopData->code1,
                '[[UserFirstName]]'              => $currUserFname,
                '[[UserLastName]]'               => $currUserLname,
                '[[UserEmail]]'                  => $currUserEmail,
                '[[Sprint]]'                     => isset($questionData->consultationStep->consultationSprint->title) ? $questionData->consultationStep->consultationSprint->title : '',
                '[[Step]]'                       => isset($questionData->consultationStep->title) ? $questionData->consultationStep->title : '',
                '[[ReinventUrl]]'                => $mailData['mail']['reinvent_url'],
                '[[AccountLongName]]'            => $mailData['mail']['acc_long_name'],
                '[[AccountShortName]]'           => $mailData['mail']['acc_short_name'],
                '[[staticSiteUrl]]'              => $mailData['mail']['staticSiteUrl'],
                '[[ConsultationName]]'           => $mailData['mail']['consultation'],
                '[[ConsultationVeryLongName]]'   => $mailData['mail']['ConsultationVeryLongName'],
                '[[CommitteeName]]'              => $mailData['mail']['committe_name'],
                '[[CommitteeSecFirstName]]'      => $mailData['mail']['CommitteeSecFirstName'],
                '[[CommiteeSecLastName]]'        => $mailData['mail']['CommiteeSecLastName'],
                '[[CommiteeSecEmail]]'           => isset($mailData['mail']['CommiteeSecEmail']) ? $mailData['mail']['CommiteeSecEmail'] : '',
                '[[CommitteePresidentFullName]]' => $mailData['mail']['committe_PresidentFullName'],
                '[[CommitteevalidatorFullName]]' => $mailData['mail']['committe_ValidatorFullName'],
                '[[CommitteeValidatorEmail]]'    => $mailData['mail']['committe_ValidatorEmail'],
                '[[CommitteePresidentEmail]]'    => $mailData['mail']['committe_PresidentEmail'],
                '[[WorkshopLongName]]'           => $workshopData->workshop_name,
                '[[WorkshopName]]'               => $workshopData->workshop_name,
                '[[WorkshopSecFirstName]]'       => $preFirstName,
                '[[WorkshopSecLastName]]'        => $preLastName,
                '[[WorkshopSecEmail]]'           => $preEmail,
                '[[WorkshopPresidentFullName]]'  => $member['p']['fname'] . ' ' . $member['p']['lname'],
                '[[WorkshopvalidatorFullName]]'  => $member['v']['fname'] . ' ' . $member['v']['lname'],
                '[[ValidatorEmail]]'             => $member['v']['email'],
                '[[PresidentEmail]]'             => $member['p']['email'],
                '[[OrgName]]'                    => $getOrgDetail->name_org,
                '[[SignatoryFname]]'             => isset($WorkshopSignatory['signatory_fname']) ? $WorkshopSignatory['signatory_fname'] : '',
                '[[Signatorylname]]'             => isset($WorkshopSignatory['signatory_lname']) ? $WorkshopSignatory['signatory_lname'] : '',
                '[[SignatoryPossition]]'         => isset($WorkshopSignatory['signatory_possition']) ? $WorkshopSignatory['signatory_possition'] : '',
                '[[SignatoryEmail]]'             => isset($WorkshopSignatory['signatory_email']) ? $WorkshopSignatory['signatory_email'] : '',
                '[[SignatoryPhone]]'             => isset($WorkshopSignatory['signatory_phone']) ? $WorkshopSignatory['signatory_phone'] : '',
                '[[SignatoryMobile]]'            => isset($WorkshopSignatory['signatory_mobile']) ? $WorkshopSignatory['signatory_mobile'] : '',
            ];

            $subject = str_replace(array_keys($keywords), array_values($keywords), json_decode($setting->setting_value)->email_subject);
            // $mailData = $keywords;
            $mailData['mail']['RecipientFirstname'] = (($participant) ? $participant->fname : '');
            $mailData['mail']['Recipient LastName'] = (($participant) ? $participant->lname : '');
            $mailData['mail']['RecipientEmail'] = (($participant) ? $participant->email : '');
            $mailData['mail']['WorkshopName'] = $workshopData->workshop_name;
            $mailData['mail']['WorkshopSecFirstName'] = $preFirstName;
            $mailData['mail']['WorkshopSecLastName'] = $preLastName;
            $mailData['mail']['WorkshopSecEmail'] = $preEmail;
            $mailData['mail']['subject'] = $subject;
            $mailData['mail']['email'] = 'sourabh@sharabh.com';
            $mailData['mail']['firstname'] = $currUserFname;
            $mailData['mail']['lastname'] = $currUserLname;
            $mailData['mail']['workshop_data'] = $workshopData;
            $mailData['mail']['template_setting'] = $setting->setting_key;
            $mailData['mail']['participant'] = $participant;
            // $mailData['mail']['url'] = $participant;
            return ($mailData);

        }

        /**
         * @param $key
         * @param string $lang
         * @return mixed
         * this function is used to get setting of given key with current language
         */
        public
        function getSetting($key, $lang = '')
        {
//        check lang in user setting
            if (!$lang && isset(Auth::user()->setting) && !empty(Auth::user()->setting)) {
                $lang = json_decode(Auth::user()->setting)->lang;
            } else if (!$lang && isset($_SESSION['lang'])) { // check lang in session
                $lang = $_SESSION['lang'];
            }
            $lang = ($lang == 'FR') ? '_FR' : '_EN';
            $key = $key . $lang;
            $data = Setting::where('setting_key', $key)->first();
            return ($data) ? json_decode($data) : $data;
        }


        /**
         * @param $request
         * @return array
         * @throws \App\Exceptions\CustomValidationException
         *
         */
        public
        function addUser($request)
        {
            $tenancy = app(\Hyn\Tenancy\Environment::class);
            $hostname = $tenancy->hostname()['fqdn'];
            //get workshopId based on Post Id
            $consultation = $this->getActive();
            if (!isset($consultation->uuid)) {
                return ['status' => FALSE, 'msg' => __('message.active_consultation')];
            } elseif (isset($consultation->public_reinvent) && $consultation->public_reinvent == 0) {
                return ['status' => FALSE, 'msg' => __('message.public_reinvent')];
            }
            DB::connection('tenant')->beginTransaction();

            $domain = strtok($hostname, '.');
            //getting hostcode for passcode
            $hostCode = \DB::connection('mysql')->table('hostname_codes')->where(['fqdn' => $hostname])->first(['hash']);
            $randCode = generateRandomValue(3);
            $newCode = setPasscode($hostCode->hash, $randCode);
            //adding user in system
            $user = UserService::getInstance()
                ->register($request, FALSE, [
                    'fname'      => $request->fname,
                    'lname'      => $request->lname,
                    'email'      => $request->email,
                    'password'   => Hash::make($request->email),
                    'role'       => 'M2',
                    'on_off'     => 0,
                    'hash_code'  => $newCode['hashCode'],
                    'login_code' => $newCode['userCode'],
                ]);

            if (!isset($request->union_id)) {
                $union = Entity::updateOrCreate([
                    'long_name'      => $request->union_name,
                    'entity_type_id' => 3,
                    //'created_by'     => Auth::user()->id,
                ], [
                    'long_name'      => $request->union_name,
                    'short_name'     => $request->union_name,
                    'entity_type_id' => 3,
                    //'created_by'     => Auth::user()->id,
                ]);
            }

            if (isset($request->class_uuid) && !empty($request->class_uuid)) {
                $position = ConsultationSignUpClassPosition::where('consultation_sign_up_class_uuid', $request->class_uuid)->where('id', $request->position)->first(['positions', 'id']);
                $label = isset($position->positions) ? $position->positions : '';
                $id = isset($position->id) ? $position->id : NULL;
                $entityUser = [
                    'user_id'                                 => $user->id,
                    'entity_id'                               => (!isset($request->union_id) ? $union->id : $request->union_id),
                    'entity_label'                            => isset($label) ? $label : $request->position,
                    'consultation_sign_up_class_positions_id' => $id,
                ];
            } else {
                $entityUser = [
                    'user_id'      => $user->id,
                    'entity_id'    => (!isset($request->union_id) ? $union->id : $request->union_id),
                    'entity_label' => isset($request->union_position) ? $request->union_position : $request->position,
                ];
            }

            $eventUser = EntityUser::updateOrCreate(
                ['user_id'   => $user->id,
                 'entity_id' => (!isset($request->union_id) ? $union->id : $request->union_id)],
                $entityUser);

            //adding mail code in signup temp table
            $code = genRandomNum(6);
            $lastId = Signup::insertGetId(['email' => $request->email, 'code' => $code]);

            //defining url of verification page
//            /#/consultation_id/registration-process/?email=ramayudhya@mailinator.com&userid=NjA2
            if (!empty(env('REINVENT_URL'))) {
                $redirectUrl = env('HOST_TYPE') . $domain . '.' . env('REINVENT_URL') . '/#/' . 'registration-process/' . ($request->email) . '/' . base64_encode($lastId);
            } else {
                $redirectUrl = env('HOST_TYPE') . $domain . '.' . 're-invent.solutions' . '/#/' . 'registration-process/' . ($request->email) . '/' . base64_encode($lastId);
            }
            //preparing data to send mail to particular user
            $userData = ['fname' => '', 'lname' => ''];
            $userData['fname'] = $user->fname;
            $userData['lname'] = $user->lname;
            $userData['email'] = $user->email;

            $data = $this->prepareEmailData((object)$userData, $consultation->workshop_id, 'resilience_validation_code', $consultation, $hostname);

            $lang = session()->has('lang') ? session()->get('lang') : "FR";
            $type = ($lang == 'FR') ? 'resilience_validation_code_FR' : 'resilience_validation_code_EN';
            $data['mail']['email'] = $user->email;
            $data['mail']['firstname'] = $request->fname;
            $data['mail']['otp'] = $code;
            $data['mail']['path'] = $redirectUrl;
            $data['mail']['type'] = $type;
            $data['mail']['candidateId'] = $user->id;
            $core = app(CoreController::class);
            if ($core->SendEmail($data, 'resilience_verification_email')) {
                DB::connection('tenant')->commit();
                return ['status' => TRUE, 'code' => 1, 'msg' => 'Email sent', 'email' => $user->email, 'user_id' => base64_encode($lastId), 'redirectUrl' => $redirectUrl];
            } else {
                DB::connection('tenant')->rollback();
                return ['status' => FALSE, 'code' => 1, 'msg' => __('message.email_not_sent'), 'type' => 'email', 'redirectUrl' => $redirectUrl];
            }
        }

        /**
         * @param $workshop_data
         * @param $key
         * @param array $data
         * @return array
         */
        function getMailData($workshop_data, $key, $data = [])
        {
            // var_dump($workshop_data);die;
            $currUserFname = (Auth::check()) ? Auth::user()->fname : '';
            $currUserLname = (Auth::check()) ? Auth::user()->lname : '';
            $currUserEmail = (Auth::check()) ? Auth::user()->email : '';
            $settings = getSettingData($key);
            $getOrgDetail = getOrgDetail();
            $member = workshopValidatorPresident($workshop_data);
            $wsetting = getWorkshopSettingData($workshop_data->id);
            $WorkshopSignatory = getWorkshopSignatoryData($workshop_data->id);
            $keywords = [
                '[[UserFirsrName]]', '[[UserLastName]]', '[[UserEmail]]', '[[WorkshopLongName]]', '[[WorkshopShortName]]', '[[WorkshopPresidentFullName]]',
                // '[[WorkshopvalidatorFullName]]',
                '[[ValidatorEmail]]', '[[PresidentEmail]]', '[[OrgName]]',
                '[[SignatoryFname]]',
                '[[Signatorylname]]',
                '[[SignatoryPossition]]',
                '[[SignatoryEmail]]',
                '[[SignatoryPhone]]',
                '[[SignatoryMobile]]',
                '[[candidateFN]]',
                '[[candidateLN]]',
                '[[candidateCompanyName]]',
                '[[candidateEmail]]',
                '[[candidatePhone]]',
                '[[CandidateAddress]]',
            ];
            $values = [
                $currUserFname, $currUserLname, $currUserEmail, $workshop_data->workshop_name, $workshop_data->code1, $member['p']['fullname'],
                //  $member['v']['fullname'],
                $member['v']['email'], $member['p']['email'], $getOrgDetail->name_org,
                isset($WorkshopSignatory['signatory_fname']) ? $WorkshopSignatory['signatory_fname'] : '',
                isset($WorkshopSignatory['signatory_lname']) ? $WorkshopSignatory['signatory_lname'] : '',
                isset($WorkshopSignatory['signatory_possition']) ? $WorkshopSignatory['signatory_possition'] : '',
                isset($WorkshopSignatory['signatory_email']) ? $WorkshopSignatory['signatory_email'] : '',
                isset($WorkshopSignatory['signatory_phone']) ? $WorkshopSignatory['signatory_phone'] : '',
                isset($WorkshopSignatory['signatory_mobile']) ? $WorkshopSignatory['signatory_mobile'] : '',
                isset($data['fname']) ? $data['fname'] : '',
                isset($data['lname']) ? $data['lname'] : '',
                isset($data->userSkillCompany->text_input) ? $data->userSkillCompany->text_input : '',
                isset($data['email']) ? $data['email'] : '',
                isset($data['tel']) ? $data['tel'] : (isset($data['phone']) ? $data['phone'] : ''),
                isset($data['address']) ? $data['address'] : '',
            ];

            $subject = (str_replace($keywords, $values, $settings->email_subject));
            $stepid = getFirstStepId();
            // $route_members = route('redirect-app-url', ['url' => str_rot13('organiser/commissions/' . $workshop_data->id . '/members')]);
            $route_members = route('redirect-app-url', ['url' => str_rot13('qualification/registration-form')]);
            return ['subject' => $subject, 'route_members' => $route_members];
        }

        /**
         * @param $data
         * @return array
         * this will check that given code is valid or not,code which we send at the time of otp mail.
         */
        public
        function checkCode($data)
        {
            $data->all();
            $res = Signup::where('id', base64_decode($data['id']))->where('email', $data['email'])->where('code', $data['code']);
            if ($res->first()) {
                $user = User::where('email', $data['email'])->first();
                // dd($user);
                if (isset($user->id)) {
                    $member = $this->addMember($data);
                    if ($member['status']) {
                        $user->on_off = 1;
                        $user->save();
                        $res->delete();
                        return ['status' => TRUE, 'msg' => __('message.code_not_match'), 'token' => $user->createToken('check')->accessToken];
                    } else {
                        return ['status' => FALSE, 'msg' => __('message.code_not_match')];
                    }

                } else {
                    return ['status' => FALSE, 'msg' => __('message.code_not_match')];
                }
            } else {
                return ['status' => FALSE, 'msg' => __('message.code_not_match')];
            }
        }

        /**
         * @param $request
         * @return array
         * this will add passed user to particular workshop,which we get from active consultation
         */
        public
        function addMember($request)
        {
            try {
                //get workshopId based on Post Id
                $consultation = $this->getActive();
                if (!isset($consultation->uuid)) {
                    return ['status' => FALSE, 'msg' => 'No active Consultation'];
                }
                $request->merge(['email_send' => 0, 'resilience_mail_send' => 0, 'workshop_id' => $consultation->workshop_id, 'firstname' => stripcslashes($request->firstname), 'lastname' => stripcslashes($request->lastname)]);

                DB::connection('tenant')->beginTransaction();
                $workshop = app(\App\Http\Controllers\WorkshopController::class);
                $reponse = $workshop->addMember($request);
                $reponse = json_decode($reponse->getContent());
                if (isset($reponse->status) && $reponse->status) {
                    //checking company is created or not
                    DB::connection('tenant')->commit();
                    return ['status' => TRUE, 'msg' => __('message.memberCreated')];
                } else {
                    DB::connection('tenant')->rollback();
                    return ['status' => FALSE, 'msg' => 'Something Wrong Happens'];
                }
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return ['status' => FALSE, 'msg' => 'Internal server error in adding Workshop Member ' . $e->getMessage()];
            }
        }

        /**
         * @param $request
         * @return mixed
         */
        public
        function saveMetaData($request)
        {

            $consultation = ConsultationAnswerUser::updateOrCreate([
                'user_id' => isset($request->user()->id) ? $request->user()->id : Auth::id(), 'consultation_uuid' => $request->get('consultation_uuid'),
            ], [
                'user_id'           => isset($request->user()->id) ? $request->user()->id : Auth::id(),
                'consultation_uuid' => $request->consultation_uuid,
                'answered'          => $this->checkConsultationIsAnswered($request),
                'answer_meta_data'  => json_encode([
                    'date'        => Carbon::now()->format('Y-m-d'),
                    'question_id' => $request->has('consultation_question_id') ? $request->get('consultation_question_id') : NULL,
                ]),
            ]);
            return $consultation;
        }

        /**
         * @param $request
         * @return int
         */
        public
        function checkConsultationIsAnswered($request)
        {
            $consultation = Consultation::with('consultationSprint:id,consultation_uuid,is_accessible')->find($request->get('consultation_uuid'), ['uuid', 'allow_to_go_back']);
            $isAccessible = FALSE;
            $allTitle = collect($consultation->consultationSprint)->reject(function ($item, $k) use (&$isAccessible, $consultation) {
                if ($item->is_accessible == 1) {
                    $isAccessible = TRUE;
                }
                if ($consultation->allow_to_go_back == 0 && !($isAccessible)) {
                    return $item;
                }
                if ($isAccessible && !($item->is_accessible == 1)) {
                    return $item;
                }
            })->values();
            $questions = ConsultationQuestion::whereHas('consultationStep', function ($q) use ($allTitle) {
                $q->where('active', 1)->whereHas('consultationSprint', function ($qq) use ($allTitle) {
                    $qq->whereIn('id', $allTitle->pluck('id'));
                });
            })->whereNotIn('consultation_question_type_id', [17]);

            $answeredQuestions = ConsultationAnswer::whereIn('consultation_question_id', $questions->pluck('id'))->where('user_id', isset($request->user()->id) ? $request->user()->id : Auth::id())->where('consultation_uuid', $request->get('consultation_uuid'))->count();

            if ($questions->count() > $answeredQuestions) {
                return 0;
            } else {
                return 1;
            }
        }

        /**
         * @return mixed
         * this will return current active Reinvent consultation for account
         */
        public
        function getActive()
        {
            $activeConsultation = Consultation::where(function ($query) {
                $query->whereDate('start_date', '<=', Carbon::today()->format('Y-m-d'))->whereDate('end_date', '>=', Carbon::today()->format('Y-m-d'));
            })->where('is_reinvent', 1)->first();
            return $activeConsultation;
        }


        /**
         * This will return user belongs to workshop or not , either pass workshop id or workshop in second param
         *
         * @param null $workshopId
         * @param Workshop $workshop
         * @param array $role
         * @param User $user
         * @return bool
         */
        public
        function isUserBelongsToWorkshop($workshopId = NULL, $workshop = NULL, $role = NULL, $user = NULL)
        {
            $workshopId = $workshop ? $workshop->id : $workshopId;
            $role = $role ? $role : [1, 2]; // role 1 2 represents admin
            $user = $user ? $user : Auth::user();
            return
                (boolean)
                WorkshopMeta::whereIn('role', $role)
                    ->where('workshop_id', $workshopId)
                    ->whereHas('workshop', function ($q) {
                        $q->withoutGlobalScopes();
                    })
                    ->where('user_id', $user->id)
                    ->count();
        }

        public
        function arrangeMailData($request, $consultation, $hostname)
        {
            $userData = ['fname' => '', 'lname' => ''];
            if ($request->data) {
                $user = User::find(json_decode($request->data)->id);
                $userData['fname'] = ((isset($user->fname)) ? $user->fname : '');
                $userData['lname'] = ((isset($user->lname)) ? $user->lname : '');
                $userData['email'] = ((isset($user->email)) ? $user->email : '');
            } else {
                $userData['fname'] = $request->firstname;
                $userData['lname'] = $request->lastname;
                $userData['email'] = $request->email;
            }

            foreach ($consultation->get() as $item) {
                if (isset($item->workshop->id)) {
                    $domain = strtok($hostname, '.');
                    $params[$item->uuid]['name'] = $item->name;
                    if ($item->is_reinvent) {
                        if (!empty(env('REINVENT_URL'))) {
                            $params[$item->uuid]['link'] = env('HOST_TYPE') . $domain . '.' . env('REINVENT_URL') . '/#/';
                        } else {
                            $params[$item->uuid]['link'] = env('HOST_TYPE') . $domain . '.' . 're-invent.solutions/#/';
                        }
                    } else {
                        $params[$item->uuid]['link'] = url('/#/organiser/commissions/' . $item->workshop_id . '/resilience/' . $item->uuid . '/mini-consultation');
                    }
                }
            }
            $data = $this->prepareEmailData((object)$userData, $request->workshop_id, 'email_for_late_participants', $item, $hostname);
            $data['mail']['url'] = $params;//dd($params);
            $res = event(new LateParticipants('email_template.dynamic_workshop_template', $data, $userData['email']));
            return $res;
        }

        public
        function testConsultationIsAnswered($request)
        {
            $consultation = Consultation::with('consultationSprint:id,consultation_uuid,is_accessible')->find($request->get('consultation_uuid'), ['uuid', 'allow_to_go_back']);
            $isAccessible = FALSE;
            $allTitle = collect($consultation->consultationSprint)->reject(function ($item, $k) use (&$isAccessible, $consultation) {
                if ($item->is_accessible == 1) {
                    $isAccessible = TRUE;
                }
                if ($consultation->allow_to_go_back == 0 && !($isAccessible)) {
                    return $item;
                }
                if ($isAccessible && !($item->is_accessible == 1)) {
                    return $item;
                }
            })->values();
            $questions = ConsultationQuestion::whereHas('consultationStep', function ($q) use ($allTitle) {
                $q->where('active', 1)->whereHas('consultationSprint', function ($qq) use ($allTitle) {
                    $qq->whereIn('id', $allTitle->pluck('id'));
                });
            });

            $answeredQuestions = ConsultationAnswer::whereIn('consultation_question_id', $questions->pluck('id'))->where('user_id', isset($request->user()->id) ? $request->user()->id : Auth::id())->where('consultation_uuid', $request->get('consultation_uuid'));


            return [$questions->toSql(), $questions->getBindings(), $answeredQuestions->toSql(), $answeredQuestions->getBindings()];
        }

        protected function newTags($consultation, $workshopData, &$mailData, $participant, $hostname, $questionData)
        {
            $committe = !empty($workshopData->parentWorkshop) ? $workshopData->parentWorkshop : NULL;
            $member = !empty($committe) ? workshopValidatorPresident($committe) : NULL;
            $domain = strtok($hostname, '.');
            if ($consultation->is_reinvent) {
                if (!empty(env('REINVENT_URL'))) {
                    $reinventUrl = env('HOST_TYPE') . $domain . '.' . env('REINVENT_URL') . '/#/';
                } else {
                    $reinventUrl = env('HOST_TYPE') . $domain . '.' . 're-invent.solutions/#/';
                }
            } else {
                $reinventUrl = '';
            }
            $data = Setting::where('setting_key', 'reinvent_page')->first();
            if ($data) {
                $decode = json_decode($data->setting_value);
                $website = !empty($decode) ? $decode->website : '';
            } else {
                $website = '';
            }

            $preFname = !empty($member) ? $member['p']['fname'] . ' ' . $member['p']['lname'] : '';
            $preFirstName = !empty($member) ? $member['p']['fname'] : '';
            $preLastName = !empty($member) ? $member['p']['lname'] : '';
            $validatorFname = !empty($member) ? $member['v']['fname'] . ' ' . $member['v']['lname'] : '';
            $preEmail = !empty($member) ? $member['p']['email'] : '';
            $validatorEmail = !empty($member) ? $member['v']['email'] : '';
            $sprintTitle = isset($questionData->consultationStep->consultationSprint->title) ? $questionData->consultationStep->consultationSprint->title : '';
            $stepTitle = isset($questionData->consultationStep->title) ? $questionData->consultationStep->title : '';
            $question = isset($questionData->question) ? $questionData->question : '';

            $mailData['mail']['reci_fname'] = ($participant->fname) ? $participant->fname : '';
            $mailData['mail']['reci_lname'] = ($participant->lname) ? $participant->lname : '';
            $mailData['mail']['reci_email'] = ($participant->email) ? $participant->email : '';
            $mailData['mail']['consultation'] = $consultation->name;
            $mailData['mail']['ConsultationVeryLongName'] = isset($consultation->long_name) ? $consultation->long_name : '';
            $mailData['mail']['committe_name'] = !empty($committe) ? $committe->workshop_name : '';
            $mailData['mail']['CommitteeSecFirstName'] = $preFirstName;
            $mailData['mail']['CommiteeSecLastName'] = $preLastName;
            $mailData['mail']['committe_PresidentFullName'] = $preFname;
            $mailData['mail']['committe_ValidatorFullName'] = $validatorFname;
            $mailData['mail']['committe_ValidatorEmail'] = $validatorEmail;
            $mailData['mail']['committe_PresidentEmail'] = $preEmail;
            $mailData['mail']['CommiteeSecEmail'] = $preEmail;
            $mailData['mail']['reinvent_url'] = $reinventUrl;
            $mailData['mail']['staticSiteUrl'] = $website;
            $mailData['mail']['sprint'] = $sprintTitle;
            $mailData['mail']['step'] = $stepTitle;
            $mailData['mail']['question'] = $question;
            $mailData['mail']['dateTime'] = getConsultationFormatAttribute(Carbon::now()->toDateTimeString(), 1);
            return $mailData;
        }

        /*
         * this function send mails to sec/dep/OrgAdmins
         * if any new option added by user
         * */
        public function sendNewOptionMail($request)
        {
            $question = ConsultationQuestion::with('consultationStep.consultationSprint.consultation')->where('id', $request->get('consultation_question_id'))->first(['consultation_step_id', 'id', 'consultation_question_type_id', 'question']);
            //here checking for allowed annual answer type and is_manual exist in request
            if (in_array($question->consultation_question_type_id, [3, 4, 13, 14, 15]) && $request->get('is_manual')) {
                $decode = json_decode($request->get('manual_answer'));
                $options = collect($decode)->implode('label', ', ');
                $userData = ['fname' => '', 'lname' => ''];
                $userData['fname'] = $request->user()->fname;
                $userData['lname'] = $request->user()->lname;
                $userData['email'] = $request->user()->email;
                $tenancy = app(\Hyn\Tenancy\Environment::class);
                $hostname = $tenancy->hostname()['fqdn'];

                $data = $this->prepareEmailData((object)$userData, $question->consultationStep->consultationSprint->consultation->workshop_id, 'resilience_user_manual_option', $question->consultationStep->consultationSprint->consultation, $hostname, $question);
                $data['mail']['NewOption'] = $options;
                $emails = $data['mail']['workshop_data']->meta->whereIn('role', [1, 2])->pluck('user.email')->unique();
                $orgEmail = User::where('role', 'M1')->pluck('email');
                if (!empty($orgEmail)) {
                    $emails = (collect($emails)->merge($orgEmail)->filter()->unique());
                }
                $data['mail']['emails'] = $emails->toArray();
                $core = app(\App\Http\Controllers\CoreController::class);
                ($core->SendMassEmail($data, 'dynamic_workshop_template'));
//                foreach ($emails as $k => $item) {
//                    event(new OptionMail('email_template.dynamic_workshop_template', $data, $item));
//                }

            }
        }

        /*
         * this function will remove meeting from step
         * if we delete the meeting
         * this will also check if step have multiple meeting or single
         * */
        public function removeMeeting($meetId)
        {
            $meetingStep = ConsultationStepMeeting::where('meeting_id', $meetId)->first();
            if (isset($meetingStep->id)) {
                $stepmeetingCount = ConsultationStepMeeting::where('consultation_step_id', $meetingStep->consultation_step_id)->where('meeting_id', '!=', $meetId)->count();
                if ($stepmeetingCount > 0) {
                    ConsultationStepMeeting::where('meeting_id', $meetId)->delete();
                } elseif ($stepmeetingCount == 0) {
                    ConsultationStepMeeting::where('consultation_step_id', $meetingStep->consultation_step_id)->delete();
                    ConsultationStep::where('id', $meetingStep->consultation_step_id)->delete();
                }
            }
        }

        /**
         * @param int $stepId
         * this function will check that any user
         * answered or not for the given step_id
         * @return bool
         */
        public function checkQuestionAnswered(int $stepId)
        {
            $step = ConsultationStep::where(['step_type' => 2, 'id' => $stepId]);
            if ($step->count() > 0) {
                return 1;
            }
            return 0;
        }
    }
