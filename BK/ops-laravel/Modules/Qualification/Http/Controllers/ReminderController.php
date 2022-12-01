<?php

    namespace Modules\Qualification\Http\Controllers;

    use App\Model\UserMeta;
    use App\Model\UserSkill;
    use App\User;
    use App\Workshop;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Routing\Controller;
    use Illuminate\Support\Facades\Auth;
    use Modules\Qualification\Entities\CandidateCard;
    use Modules\Qualification\Entities\QualificationUserReminder;
    use Modules\Qualification\Entities\Referrer;
    use Modules\Qualification\Entities\ReferrerField;
    use Modules\Qualification\Entities\ReviewStep;
    use Batch;
    use Modules\Qualification\Entities\ReviewStepField;
    use Modules\Qualification\Entities\QualificationReminder;
    use Modules\Qualification\Entities\Step;
    use Modules\Qualification\Services\RegistrationService;
    use Validator;


    /**
     * Class ReminderController
     * @package Modules\Qualification\Http\Controllers
     */
    class ReminderController extends Controller
    {
        /**
         * Display a listing of the resource.
         * @return Response
         */

        public function __construct()
        {
            $this->step = app(\Modules\Qualification\Http\Controllers\StepController::class);
            $this->core = app(\App\Http\Controllers\CoreController::class);
            $this->host = app(\Hyn\Tenancy\Environment::class);
        }

        /**
         * This function is used to send first renewal mail using daily cron
         * in this function we send mails to candidates
         * who's issued card validated date passed 306 days
         * and now his card instance will not  be +4 means Not divisible to 4
         */
        public function sendFirstRenewalMailCron()
        {
            $date = Carbon::today()->format('Y-m-d');
            $rejectedCards = UserMeta::where('is_final_save', config('constants.REJECTED_STATUS'))->get(['user_id']);

            $cards = CandidateCard::/*whereIn('workshop_id', [402])->*/ whereNotIn('card_instance', [4, 8, 12, 16, 20, 24])->whereNotIn('user_id', (count($rejectedCards) > 0) ? $rejectedCards->toArray() : [])->with('user.userMeta')->whereHas('user.userInfo', function ($q) {
                $q->where('is_final_save', '>', 2);
            })->whereRaw("DATE_ADD(date_of_validation, INTERVAL 306 DAY) <= '$date'")->where('is_archived', 0)->orderBy('id', 'desc')->get(['id', 'user_id', 'card_instance']);

            $emails = [];
            $userReminder = [];
            $cards->unique('user_id')->map(function ($v, $k) use (&$userReminder, $date) {
                if (isset($v->user->email) && isset($v->user->userMeta[0]->workshop_id)) {
                    $latestCard = $v->user->load('userCards');
                    if (isset($latestCard->userCards) && count($latestCard->userCards) > 0) {
                        $card = $latestCard->userCards[(count($latestCard->userCards) - 1)]->card_instance;
                    } else {
                        $card = $v->card_instance;
                    }
                    if (($card == $v->card_instance)) {
                        $emails[] = $v->user->email;
                        $workshop_data = Workshop::withoutGlobalScopes()->with('meta')->find($v->user->userMeta[0]->workshop_id);
                        //check that workshop must be active qualification
                        if (isset($workshop_data->is_qualification_workshop) && $workshop_data->is_qualification_workshop == 1 /*&& $workshop_data->id == 156*/) {
                            $dataMail = $this->getMailData($workshop_data, 'candidate_renewal_1_year_' . (!empty(session()->get('lang')) ? session()->get('lang') : 'FR'), $v->user);
                            $subject = $dataMail['subject'];
                            $hostname = $this->host->hostname()['fqdn'];
                            $domain = strtok($hostname, '.');
                            if (!empty($hostname))
                                $redirectUrl = env('HOST_TYPE') . $hostname . '/#/qualification/registration-form';
                            else
                                $redirectUrl = url('/#/qualification/registration-form');
                            $mailData['mail'] = ['subject'           => ($subject), 'emails' => $emails, 'workshop_data' => $workshop_data, 'template_setting' => 'candidate_renewal_1_year_' . (!empty(session()->get('lang')) ? session()->get('lang') : 'FR'), 'url' => $redirectUrl, 'candidate_fname' => $v->user->fname,
                                                 'candidate_lname'   => $v->user->lname,
                                                 'candidate_company' => '',
                            ];
                            $userReminder[] = ['user_id' => $v->user->id, 'type_of_email' => 1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')];
                            $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                            $this->step->resetUserMeta($v->user->id);

                            //only for testing
                            //$v->update(['date_of_validation' => $date]);
                        }
                    }
                }
            });

            if (count($userReminder) > 0)
                QualificationUserReminder::insert($userReminder);
        }

        /**
         * This function is used to send Fourth renewal mail using daily cron
         * in this function we send mails to candidates
         * who's issued card validated date passed 306 days
         * and now his card instance will be +4 means divisible to 4
         */
        public function sendFourthRenewalMailCron()
        {
            $date = Carbon::today()->format('Y-m-d');
            $rejectedCards = UserMeta::where('is_final_save', config('constants.REJECTED_STATUS'))->get(['user_id']);

            $cards = CandidateCard::/*whereIn('workshop_id', [402])->*/ whereIn('card_instance', [4, 8, 12, 16, 20, 24])->whereNotIn('user_id', (count($rejectedCards) > 0) ? $rejectedCards->toArray() : [])->
            with('user.userMeta')->whereHas('user.userInfo', function ($q) {
                $q->where('is_final_save', '>', 2);
            })->whereRaw("DATE_ADD(date_of_validation, INTERVAL 306 DAY) <= '$date'")->where('is_archived', 0)->orderBy('id', 'desc')->get(['id', 'user_id', 'card_instance']);

            $emails = [];
            $userReminder = [];
            $cards->unique('user_id')->map(function ($v, $k) use (&$userReminder, $date) {

                if (isset($v->user->email) && isset($v->user->userMeta[0]->workshop_id)) {
                    $emails[] = $v->user->email;

                    $workshop_data = Workshop::withoutGlobalScopes()->with('meta')->find($v->user->userMeta[0]->workshop_id);
                    //check that workshop must be active qualification
                    if (isset($workshop_data->is_qualification_workshop) && $workshop_data->is_qualification_workshop == 1 /*&& $workshop_data->id == 156*/) {
                        $dataMail = $this->getMailData($workshop_data, 'candidate_renewal_4_year_' . (!empty(session()->get('lang')) ? session()->get('lang') : 'FR'), $v->user);
                        $subject = $dataMail['subject'];
                        $hostname = $this->host->hostname()['fqdn'];
                        $domain = strtok($hostname, '.');
                        if (!empty($hostname))
                            $redirectUrl = env('HOST_TYPE') . $hostname . '/#/qualification/registration-form';
                        else
                            $redirectUrl = url('/#/qualification/registration-form');
                        $mailData['mail'] = ['subject'           => ($subject), 'emails' => $emails, 'workshop_data' => $workshop_data, 'template_setting' => 'candidate_renewal_4_year_' . (!empty(session()->get('lang')) ? session()->get('lang') : 'FR'), 'url' => $redirectUrl, 'candidate_fname' => $v->user->fname,
                                             'candidate_lname'   => $v->user->lname,
                                             'candidate_company' => '',
                        ];
                        $userReminder[] = ['user_id' => $v->user->id, 'type_of_email' => 4, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')];
                        $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                        $this->step->resetUserMeta($v->user->id);

                        //only for testing
                        //$v->update(['date_of_validation' => $date]);
                    }
                }
            });
            if (count($userReminder) > 0)
                QualificationUserReminder::insert($userReminder);
        }

        /**
         * This function is archive a card,who's validated date become
         * greater than 365 days
         * this function simply take card with above condition and mark it archive
         */
        public function archiveCardCron()
        {
            $date = Carbon::today()->format('Y-m-d');

            $cards = CandidateCard::whereRaw("DATE_ADD(date_of_validation, INTERVAL 365 DAY) <='$date'")->where('is_archived', 0)->orderBy('id', 'desc')->get(['id', 'user_id', 'card_instance', 'date_of_validation']);
            $cardUpdate = [];
            $cards->unique('user_id')->map(function ($v, $k) use (&$cardUpdate) {
                $cardUpdate[] = ['id' => $v->id, 'is_archived' => 1];
            });
            if (count($cardUpdate) > 0) {
                $userInstance = new CandidateCard;
                $index = 'id';
                Batch::update($userInstance, $cardUpdate, $index);
            }
        }

        /**
         * @param $workshop_data
         * @param $key
         * @param array $cid
         * @param int $refId
         * @return array
         */
        function getMailData($workshop_data, $key, $cid = [], $refId = 0)
        {
            $currUserFname = @Auth::user()->fname;
            $currUserLname = @Auth::user()->lname;
            $currUserEmail = @Auth::user()->email;
            $settings = getSettingData($key);
            $getOrgDetail = getOrgDetail();
            $member = workshopValidatorPresident($workshop_data);
            $wsetting = getWorkshopSettingData($workshop_data->id);
            $WorkshopSignatory = getWorkshopSignatoryData($workshop_data->id);
            $user = $cid;
            $registrationService = RegistrationService::getInstance();
            if (isset($user->id))
                $date = $registrationService->getDeliveryDate($user->id);

            if ($refId > 0) {
                $ref = Referrer::find($refId);
                $referrerField = ReferrerField::with('step')->where('refreer_id', $ref->id)->first();
                if ($ref != NULL) {
                    $refFname = $ref->fname;
                    $refLname = $ref->lname;
                    $refEmail = $ref->email;
                    $refCompany = $ref->company;
                    $refDomain = isset($referrerField->step->name) ? $referrerField->step->name : '-';
                }
            } else {
                $refFname = '';
                $refLname = '';
                $refEmail = '';
                $refCompany = '';
                $refDomain = '-';
            }
            // dd($date,getGrantedDomain($user->id));
            $keywords = [
                '[[UserFirsrName]]', '[[UserLastName]]', '[[UserEmail]]', '[[WorkshopLongName]]', '[[WorkshopShortName]]', '[[WorkshopPresidentFullName]]',
                '[[WorkshopvalidatorFullName]]', '[[ValidatorEmail]]', '[[PresidentEmail]]', '[[OrgName]]',

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
                '[[CardDateOfValidation]]',
                '[[CardExpirationDate]]',
                '[[listOfDomainsGranted]]',
                '[[referreeFN]]',
                '[[referreeLN]]',
                '[[referreeCompanyName]]',
                '[[DomainOfTheMagicLink]]',
            ];
            $values = [
                $currUserFname, $currUserLname, $currUserEmail, $workshop_data->workshop_name, $workshop_data->code1, $member['p']['fullname'],
                $member['v']['fullname'], $member['v']['email'], $member['p']['email'], $getOrgDetail->name_org
                , isset($WorkshopSignatory['signatory_fname']) ? $WorkshopSignatory['signatory_fname'] : '',
                isset($WorkshopSignatory['signatory_lname']) ? $WorkshopSignatory['signatory_lname'] : '',
                isset($WorkshopSignatory['signatory_possition']) ? $WorkshopSignatory['signatory_possition'] : '',
                isset($WorkshopSignatory['signatory_email']) ? $WorkshopSignatory['signatory_email'] : '',
                isset($WorkshopSignatory['signatory_phone']) ? $WorkshopSignatory['signatory_phone'] : '',
                isset($WorkshopSignatory['signatory_mobile']) ? $WorkshopSignatory['signatory_mobile'] : '',
                isset($user['fname']) ? $user['fname'] : '',
                isset($user['lname']) ? $user['lname'] : '',
                isset($user->userSkillCompany->text_input) ? $user->userSkillCompany->text_input : '',
                isset($user['email']) ? $user['email'] : '',
                isset($user['tel']) ? $user['tel'] : '',
                isset($user['address']) ? $user['address'] : '',
                isset($date['deliverydate']) ? $date['deliverydate'] : '',
                isset($date['expdeliverydate']) ? $date['expdeliverydate'] : '',
                isset($user->id) ? getGrantedDomain($user->id) : '',
                $refFname,
                $refLname,
                $refCompany,
                $refDomain,
            ];

            $subject = (str_replace($keywords, $values, $settings->email_subject));
            return ['subject' => $subject];
        }

        /**
         *
         */
        public function updateStepReviewDates()
        {
            $review = ReviewStep::all(['id', 'created_at']);
            $reviewUpdate = [];
            $review->map(function ($v, $k) use (&$reviewUpdate) {
                $reviewUpdate[] = ['id' => $v->id, 'saved_for' => $v->created_at];
            });
            if (count($reviewUpdate) > 0) {
                $userInstance = new ReviewStep;
                $index = 'id';
                Batch::update($userInstance, $reviewUpdate, $index);
            }
        }

        /**
         *
         */
        public function updateStepFieldReviewDates()
        {
            $review = ReviewStepField::all(['id', 'created_at']);
            $reviewUpdate = [];
            $review->map(function ($v, $k) use (&$reviewUpdate) {
                $reviewUpdate[] = ['id' => $v->id, 'saved_for' => $v->created_at];
            });
            if (count($reviewUpdate) > 0) {
                $userInstance = new ReviewStepField;
                $index = 'id';
                Batch::update($userInstance, $reviewUpdate, $index);
            }
        }

        /**
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         * this function is used to save qualification Reminders limit for sending mails
         *  from reminder setting
         */
        public function saveQualificationTask(Request $request)
        {
            // try{
            $validator = Validator::make($request->all(), [
                'section_id'    => 'required',
                'reminder_time' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            if ($request->section_id == 5) {

                $data = QualificationReminder::updateOrCreate(['section_id' => $request->section_id], ['section_id' => $request->section_id, 'week_reminder' => $request->reminder_time]);
            } else {
                $data = QualificationReminder::updateOrCreate(['section_id' => $request->section_id], ['section_id' => $request->section_id, 'reminder_time' => $request->reminder_time]);
            }
            if ($data) {
                return response()->json(['status' => TRUE, 'msg' => 'Reminder set']);
            } else {
                return response()->json(['status' => FALSE, 'msg' => 'Remider Not Set']);
            }
            // } catch (\Exception $e) {
            //     // DB::rollback();
            //     return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
            // }
        }


        /**
         * @param $host
         * $host is to determine the current host as we use this function
         * in cron so we pass the host so using that we set host here
         * this function we called daily to send first and fourth renewal
         */
        public function cronMail($host)
        {
            //setting up the host
            $tenancy = app(\Hyn\Tenancy\Environment::class);
            $host = $tenancy->hostname($host);
            //calling renewal mails sending functions
            $first = $this->sendFirstRenewalMailCron();
            $fourth = $this->sendFourthRenewalMailCron();
            $this->archiveCardCron();
        }

        /**
         * @param $host
         * $host is to determine the current host as we use this function
         * in cron so we pass the host so using that we set host here
         * this function we called daily to send reminder
         * first, fourth renewal and original registration
         */
        public function remindersCron($host)
        {
            //setting up the host
            $tenancy = app(\Hyn\Tenancy\Environment::class);
            $host = $tenancy->hostname($host);
            //getting reminder send limit which we set from reminder setting in OrgSetting
            $tillTime = QualificationReminder::all(['id', 'section_id', 'reminder_time']);
            $this->reminderRegistration($tillTime);
            $this->reminderRenewalFirst($tillTime);
            $this->reminderRenewalFourth($tillTime);
        }


        /**
         * @param $host
         * $host is to determine the current host as we use this function
         * in cron so we pass the host so using that we set host here
         * this is the function which we used in console to send weekly reminder
         * and check that weekly cron for sec/dep and expert is enabled or not
         * called the function on that condition
         */
        public function remindersCronWeekly($host)
        {
            //setting up the host
            $tenancy = app(\Hyn\Tenancy\Environment::class);
            $host = $tenancy->hostname($host);
            $setting = QualificationReminder::where('section_id', 5)->first(['id', 'week_reminder']);
            //checking that setting added or not
            if (isset($setting->week_reminder) && !empty($setting->week_reminder)) {
                if (!is_array($setting->week_reminder) && isJson($setting->week_reminder)) {
                    $decode = json_decode($setting->week_reminder, TRUE);
                    foreach ($decode as $k => $item) {
                        if ($item) {
                            if ($k == 'exp_reminder')
                                $this->reminderForExpert();
                            elseif ($k == 'sec_reminder')
                                $this->reminderForWkadmin();
                        }
                    }
                } else {
                    foreach ($setting->week_reminder as $k => $item) {
                        if ($item) {
                            if ($k == 'exp_reminder')
                                $this->reminderForExpert();
                            elseif ($k == 'sec_reminder')
                                $this->reminderForWkadmin();
                        }
                    }
                }
            }
        }

        /**
         * @param $host
         * $host is to determine the current host as we use this function
         * in cron so we pass the host so using that we set host here
         * this is the function which we used in console to send Referrer reminder
         */
        public function remindersCronReferrer($host)
        {
            //setting up the host
            $tenancy = app(\Hyn\Tenancy\Environment::class);
            $host = $tenancy->hostname($host);
            $tillTime = QualificationReminder::all(['id', 'section_id', 'reminder_time']);
            $this->reminderReferrer($tillTime);
        }

        /**
         * This function is used to send reminder to original card using remindersCron() fx.
         * in this function we send mails to candidates
         * who didnt submit his first card request yet
         * this will send mails in interval of 14 days
         * and time limit which set from reminder using orgSetting
         * @param $tillTime
         * $tillTime to determine send limit which we set from Qualification Reminders setting in OrgSetting
         */
        public function reminderRegistration($tillTime)
        {
            $date = Carbon::today();
            $tillTime = $tillTime->where('section_id', 1)->first();
            $users = User::where(['on_off' => 1, 'sub_role' => 'C1'])->with('userInfo', 'userMeta', 'userRegistrationReminder', 'userSkillCompany')->withCount('userCards')->whereHas('userInfo', function ($b) {
                $b->where('is_final_save', 0);
            })->get();

            $userReminder = [];
            $registrationService = RegistrationService::getInstance();
            $users->unique('id')->map(function ($v, $k) use (&$userReminder, $tillTime, $date, &$registrationService) {
                if (isset($v->email) && isset($v->userMeta[0]->workshop_id) && $v->user_cards_count == 0) {

                    $emails[] = $v->email;
                    $workshop_data = Workshop::withoutGlobalScopes()->with('meta')->find($v->userMeta[0]->workshop_id);
                    //check that workshop must be active qualification
                    if (isset($workshop_data->is_qualification_workshop) && $workshop_data->is_qualification_workshop == 1 /*&& $workshop_data->id == 156*/) {
                        $dataMail = $this->getMailData($workshop_data, 'reminder_welcome_email_' . (!empty(session()->get('lang')) ? session()->get('lang') : 'FR'), $v);
                        $subject = $dataMail['subject'];
                        $hostname = $this->host->hostname()['fqdn'];
                        $domain = strtok($hostname, '.');
                        if (!empty($hostname))
                            $redirectUrl = env('HOST_TYPE') . $hostname . '/#/qualification/registration-form';
                        else
                            $redirectUrl = url('/#/qualification/registration-form');
                        $mailData['mail'] = ['subject'           => ($subject), 'emails' => $emails, 'workshop_data' => $workshop_data, 'template_setting' => 'reminder_welcome_email_' . (!empty(session()->get('lang')) ? session()->get('lang') : 'FR'), 'url' => $redirectUrl,
                                             'candidate_fname'   => $v->fname,
                                             'candidate_lname'   => $v->lname,
                                             'candidate_company' => isset($v->userSkillCompany->text_input) ? $v->userSkillCompany->text_input : '', 'date' => $registrationService->getDeliveryDate($v->id), 'domain' => getGrantedDomain($v->id),
                        ];

                        if ($v->userRegistrationReminder->count() >= 1 && ($tillTime->reminder_time > $v->userRegistrationReminder->count())) {

                            //"DATE_ADD(created_at, INTERVAL 14 DAY) <= '" . $date->format('Y-m-d') . "'"
                            $checkDate = $v->userRegistrationReminder;
                            $key = $checkDate->search(function ($item, $key) use ($date) {
//                                return Carbon::parse($item->created_at)->addMinutes(15)->lte($date);
                                return Carbon::parse($item->created_at)->addDays(14)->lte($date);
                            });
                            if ($key !== FALSE) {
                                $userReminder[] = ['user_id' => $v->id, 'type_of_email' => 5, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')];
                                $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                            }

                        } elseif (($v->userRegistrationReminder->count() == 0)) {
                            $userReminder[] = ['user_id' => $v->id, 'type_of_email' => 5, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')];
                            $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                        }
                    }
                }
            });
            if (count($userReminder) > 0)
                QualificationUserReminder::insert($userReminder);
        }

        /**
         * This function is used to send First renewal reminder mail using remindersCron() fx.
         * in this function we send mails to candidates
         * who got the renewal mail before 14 days
         * this will send mails in interval of 14 days
         * and time limit which set from reminder using orgSetting
         * @param $tillTime
         * $tillTime to determine send limit which we set from Qualification Reminders setting in OrgSetting
         */
        public function reminderRenewalFirst($tillTime)
        {
            $date = Carbon::today();
            $tillTime = $tillTime->where('section_id', 2)->first();

            $emailsFirstRenewal = QualificationUserReminder::where('type_of_email', 1)->with('user.userMeta', 'user.userFirstReminder', 'user.userSkillCompany')->whereYear('created_at', $date->format('Y'))->whereRaw("DATE_ADD((created_at), INTERVAL 14 DAY) <= '" . Carbon::now()->format('Y-m-d H:i:s') . "'")->get();
            //checking users status in meta submitted or not
            $rejectedCards = UserMeta::where('is_final_save', config('constants.REJECTED_STATUS'))->get(['user_id']);

            $userMeta = UserMeta::whereIn('user_id', $emailsFirstRenewal->pluck('user_id'))->where('is_final_save', 0)->whereNotIn('user_id', (count($rejectedCards) > 0) ? $rejectedCards->toArray() : [])->get();
            $emailsFirstRenewal = $emailsFirstRenewal->whereIn('user_id', $userMeta->pluck('user_id'));
            $emails = [];
            $userReminder = [];
            $registrationService = RegistrationService::getInstance();
            $emailsFirstRenewal->unique('user_id')->map(function ($v, $k) use (&$userReminder, $date, $tillTime, &$registrationService) {
                if (isset($v->user->email) && isset($v->user->userMeta[0]->workshop_id)) {
                    $emails[] = $v->user->email;
                    $workshop_data = Workshop::withoutGlobalScopes()->with('meta')->find($v->user->userMeta[0]->workshop_id);
                    //check that workshop must be active qualification
                    if (isset($workshop_data->is_qualification_workshop) && $workshop_data->is_qualification_workshop == 1 /*&& $workshop_data->id == 156*/) {
                        $dataMail = $this->getMailData($workshop_data, 'reminder_candidate_renewal_1_year_' . (!empty(session()->get('lang')) ? session()->get('lang') : 'FR'), $v->user);

                        $subject = $dataMail['subject'];
                        $hostname = $this->host->hostname()['fqdn'];
                        $domain = strtok($hostname, '.');
                        if (!empty($hostname))
                            $redirectUrl = env('HOST_TYPE') . $hostname . '/#/qualification/registration-form';
                        else
                            $redirectUrl = url('/#/qualification/registration-form');
                        $mailData['mail'] = ['subject'           => ($subject), 'emails' => $emails, 'workshop_data' => $workshop_data, 'template_setting' => 'reminder_candidate_renewal_1_year_' . (!empty(session()->get('lang')) ? session()->get('lang') : 'FR'), 'url' => $redirectUrl,
                                             'candidate_fname'   => $v->user->fname,
                                             'candidate_lname'   => $v->user->lname,
                                             'candidate_company' => isset($v->user->userSkillCompany->text_input) ? $v->user->userSkillCompany->text_input : '', 'date' => $registrationService->getDeliveryDate($v->user->id), 'domain' => getGrantedDomain($v->user->id),
                        ];

                        if ($v->user->userFirstReminder->count() >= 1 && ($tillTime->reminder_time > $v->user->userFirstReminder->count())) {

                            $checkDate = $v->user->userFirstReminder;
                            $key = $checkDate->search(function ($item, $key) use ($date) {
//                                return Carbon::parse($item->created_at)->addMinutes(15)->lte($date);
                                return Carbon::parse($item->created_at)->addDays(14)->lte($date);
                            });
                            if ($key !== FALSE) {
                                $userReminder[] = ['user_id' => $v->user->id, 'type_of_email' => 2, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')];
                                $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                            }

                        } elseif (($v->user->userFirstReminder->count() == 0)) {
                            $userReminder[] = ['user_id' => $v->user->id, 'type_of_email' => 2, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')];
                            $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                        }
                    }
                }
            });

            if (count($userReminder) > 0)
                QualificationUserReminder::insert($userReminder);
        }

        /**
         * This function is used to send Fourth renewal reminder mail using remindersCron() fx.
         * in this function we send mails to candidates
         * who got his +4 renewal mail before 14 days
         * this will send mails in interval of 14 days
         * and time limit which set from reminder using orgSetting
         * @param $tillTime
         * $tillTime to determine send limit which we set from Qualification Reminders setting in OrgSetting
         */
        public function reminderRenewalFourth($tillTime)
        {
            $date = Carbon::today();
            $registrationService = RegistrationService::getInstance();
            $tillTime = $tillTime->where('section_id', 3)->first();
            $emailsFirstRenewal = QualificationUserReminder::where('type_of_email', 4)->with('user.userMeta', 'user.userSkillCompany')->with(['user.userFourthReminder' => function ($q) use ($date) {
                //$q->whereRaw("DATE_ADD(created_at, INTERVAL 14 DAY) <= '$date->format(Y-m-d)'");
            }])->whereYear('created_at', $date->format('Y'))->whereRaw("DATE_ADD((created_at), INTERVAL 14 DAY) <= '" . Carbon::now()->format('Y-m-d H:i:s') . "'")->get();
            //checking users status in meta submitted or not
            $rejectedCards = UserMeta::where('is_final_save', config('constants.REJECTED_STATUS'))->get(['user_id']);

            $userMeta = UserMeta::whereIn('user_id', $emailsFirstRenewal->pluck('user_id'))->where('is_final_save', 0)->whereNotIn('user_id', (count($rejectedCards) > 0) ? $rejectedCards->toArray() : [])->get();
            $emailsFirstRenewal = $emailsFirstRenewal->whereIn('user_id', $userMeta->pluck('user_id'));
            $emails = [];
            $userReminder = [];
            $emailsFirstRenewal->unique('user_id')->map(function ($v, $k) use (&$userReminder, $date, $tillTime, &$registrationService) {
                if (isset($v->user->email) && isset($v->user->userMeta[0]->workshop_id)) {
                    $emails[] = $v->user->email;
                    $workshop_data = Workshop::withoutGlobalScopes()->with('meta')->find($v->user->userMeta[0]->workshop_id);
                    //check that workshop must be active qualification
                    if (isset($workshop_data->is_qualification_workshop) && $workshop_data->is_qualification_workshop == 1 /*&& $workshop_data->id == 156*/) {
                        $dataMail = $this->getMailData($workshop_data, 'reminder_candidate_renewal_4_year_' . (!empty(session()->get('lang')) ? session()->get('lang') : 'FR'), $v->user);
                        $subject = $dataMail['subject'];
                        $hostname = $this->host->hostname()['fqdn'];
                        $domain = strtok($hostname, '.');
                        if (!empty($hostname))
                            $redirectUrl = env('HOST_TYPE') . $hostname . '/#/qualification/registration-form';
                        else
                            $redirectUrl = url('/#/qualification/registration-form');
                        $mailData['mail'] = ['subject'           => ($subject), 'emails' => $emails, 'workshop_data' => $workshop_data, 'template_setting' => 'reminder_candidate_renewal_4_year_' . (!empty(session()->get('lang')) ? session()->get('lang') : 'FR'), 'url' => $redirectUrl,
                                             'candidate_fname'   => $v->user->fname,
                                             'candidate_lname'   => $v->user->lname,
                                             'candidate_company' => isset($v->user->userSkillCompany->text_input) ? $v->user->userSkillCompany->text_input : '', 'date' => $registrationService->getDeliveryDate($v->user->id), 'domain' => getGrantedDomain($v->user->id),
                        ];

                        if ($v->user->userFourthReminder->count() >= 1 && ($tillTime->reminder_time > $v->user->userFourthReminder->count())) {

                            $checkDatev = $v->user->userFourthReminder;
                            $key = $checkDatev->search(function ($item, $key) use ($date) {
//                                return Carbon::parse($item->created_at)->addMinutes(15)->lte($date);
                                return Carbon::parse($item->created_at)->addDays(14)->lte($date);
                            });
                            if ($key !== FALSE) {
                                $userReminder[] = ['user_id' => $v->user->id, 'type_of_email' => 3, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')];
                                $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                            }

                        } elseif (($v->user->userFourthReminder->count() == 0)) {
                            $userReminder[] = ['user_id' => $v->user->id, 'type_of_email' => 3, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')];
                            $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                        }
                    }
                }
            });

            if (count($userReminder) > 0)
                QualificationUserReminder::insert($userReminder);
        }

        /**
         * This function is used to send Referrer  reminder mail using remindersCronReferrer() fx.
         * in this function we send mails to referrer
         * who got the magic link and not reply on that yet
         * this will send mails in interval of 14 days
         * and time limit which set from reminder using orgSetting
         * @param $tillTime
         * $tillTime to determine send limit which we set from Qualification Reminders setting in OrgSetting
         */
        public function reminderReferrer($tillTime)
        {
            $date = Carbon::today();

            $ref = ReferrerField::with('candidate.userMeta', 'candidate', 'referrer.userReffrerReminder')->whereNull('file')->orderByDesc('id')->get();

            $emails = [];
            $userReminder = [];
            $ref->map(function ($v, $k) use (&$userReminder, $date, $tillTime) {
                if (isset($v->referrer->email) && isset($v->candidate->userMeta[0]->workshop_id) && (isset($v->candidate->userInfo->is_final_save) && $v->candidate->userInfo->is_final_save < 3)) {
                    $emails[] = $v->referrer->email;
                    $workshop_data = Workshop::withoutGlobalScopes()->with('meta')->find($v->candidate->userMeta[0]->workshop_id);
                    //check that workshop must be active qualification
                    if (isset($workshop_data->is_qualification_workshop) && $workshop_data->is_qualification_workshop == 1 /*&& $workshop_data->id == 156*/) {
                        $dataMail = $this->getMailData($workshop_data, 'reminder_magic_link_submit_' . (!empty(session()->get('lang')) ? session()->get('lang') : 'FR'), $v->candidate, $v->referrer->id);
                        $subject = $dataMail['subject'];
                        $hostname = $this->host->hostname()['fqdn'];
                        $domain = strtok($hostname, '.');
                        if (!empty($hostname))
                            $redirectUrl = env('HOST_TYPE') . $hostname . '/referrer/external-referrer-view/' . encrypt($v->id);
                        else
                            $redirectUrl = url('/referrer/external-referrer-view/' . encrypt($v->id));


                        $mailData['mail'] = ['subject'   => ($subject), 'emails' => $emails, 'workshop_data' => $workshop_data, 'template_setting' => 'reminder_magic_link_submit_' . (!empty(session()->get('lang')) ? session()->get('lang') : 'FR'), 'url' => $redirectUrl,
                                             'firstname' => $v->referrer->fname,
                                             'lastname'  => $v->referrer->lname,
                                             'company'   => $v->referrer->company,
                        ];

                        if (!empty($v->referrer->userReffrerReminder) && $v->referrer->userReffrerReminder->count() >= 1 && isset($tillTime->reminder_time) && $tillTime->reminder_time > $v->referrer->userReffrerReminder->count()) {
                            $checkDate = $v->referrer->userReffrerReminder->whereRaw("DATE_ADD(created_at, INTERVAL 14 DAY) <= '" . $date->format('Y-m-d') . "'");
                            if (isset($checkDate->id)) {
                                $userReminder[] = ['user_id' => $v->candidate->id, 'type_of_email' => 6, 'created_at' => Carbon::now()->format('Y-m-d H:i:s'), 'referrer_id' => $v->referrer->id];
                                $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                            }

                        } elseif (empty($v->referrer->userReffrerReminder)/*$v->referrer->userReffrerReminder->count() == 0)*/) {
                            $userReminder[] = ['user_id' => $v->candidate->id, 'type_of_email' => 6, 'created_at' => Carbon::now()->format('Y-m-d H:i:s'), 'referrer_id' => $v->referrer->id];
                            $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                        }
                    }
                }
            });

            if (count($userReminder) > 0)
                QualificationUserReminder::insert($userReminder);
        }

        /**
         * This function is used to send  mails using remindersCronWeekly() fx.
         * in this function we send mails to Expert
         * who have pending review request
         * this will send mails every week on sunday
         * it check mails are enable or not which set from Qualification Reminders using orgSetting
         */
        public function reminderForExpert()
        {
            $registrationService = RegistrationService::getInstance();
            // get all workshop active qualification workkshop
            $workshop = Workshop::withoutGlobalScopes()->with('meta')->where('is_qualification_workshop', 1)->whereIn('id', [156, 839])->get();
            //  getting all user of cards waiting for validation
            $valid = UserMeta::with('user.userSkillCompany')->where('is_final_save', 1)->get();
            $workshop->map(function ($v, $k) use (&$valid, &$registrationService) {
                // Array of userIds of each workshops for checking cards waiting for validation user id is related that workshop
                $usersId = $v->meta->pluck('user.id')->toArray();
                // Array of all experts Emails
                $expertEmails = $v->meta->where('role', 0)->pluck('user.email')->toArray();
                $emails = array_unique($expertEmails);
                $workshop_data = $v;
                $validCount = 0;
                $valid->whereIn('user_id', $usersId)->map(function ($val, $key) use (&$usersId, &$workshop_data, &$emails, &$registrationService, &$validCount) {
                    //checking cards waiting for validation user id is related that workshop

                    if (in_array($val->user_id, $usersId)) {
                        $validCount = $validCount + 1;
                    }
                });
                //

                if ($validCount) {
                    $lang = session()->has('lang') ? session()->get('lang') : 'FR';
                    $dataMail = $this->getMailData($workshop_data, 'reminder_expert_' . $lang, []);
                    $subject = $dataMail['subject'];
                    $hostname = $this->host->hostname()['fqdn'];
                    $domain = strtok($hostname, '.');
                    if (!empty($hostname))
                        $redirectUrl = env('HOST_TYPE') . $hostname . '/#/qualification/' . $v->id . '/candidates';
                    else
                        $redirectUrl = url('/#/qualification/' . $v->id . '/candidates');
                    $mailData['mail'] = ['subject'           => ($subject), 'emails' => $emails, 'workshop_data' => $workshop_data, 'template_setting' => 'reminder_expert_' . $lang, 'url' => $redirectUrl,
                                         'candidate_fname'   => '',
                                         'candidate_lname'   => '',
                                         'candidate_company' => '', 'date' => '', 'domain' => '',
                    ];
                    $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                }
            });
        }

        /**
         * This function is used to send  mails using remindersCronWeekly() fx.
         * in this function we send mails to Sec/dep of workshops
         * if any request pending in Pre validation section
         * or in waiting for Final validation
         * this will send mails, every week on sunday
         * it check mails are enable or not which set from Qualification Reminders using orgSetting
         */
        public function reminderForWkadmin()
        {
            $registrationService = RegistrationService::getInstance();
            // get all workshop active qualification workkshop
            $workshop = Workshop::withoutGlobalScopes()->with('meta')->where('is_qualification_workshop', 1)->whereIn('id', [156, 839])->get();
            //  getting all user of  cards waiting ofr pre-validation
            $pre = UserMeta::with('user')->where('is_final_save', 1)->get();
            //  getting all user of cards waiting for validation
            $valid = UserMeta::with('user.userSkillCompany')->where('is_final_save', 2)->get();
            $workshop->map(function ($v, $k) use (&$valid, &$pre, &$registrationService) {
                // Array of userIds
                $usersId = $v->meta->pluck('user.id')->toArray();
                // Array of all experts Emails
                $expertEmails = $v->meta->whereIn('role', [1, 2])->pluck('user.email')->toArray();
                $emails = array_unique($expertEmails);
                $workshop_data = $v;
                $validCount = 0;
                $preCount = 0;
                $valid->whereIn('user_id', $usersId)->map(function ($val, $key) use (&$usersId, &$workshop_data, &$emails, &$registrationService, &$validCount) {
                    //checking cards waiting for validation user id is related that workshop
                    if (in_array($val->user_id, $usersId)) {
                        $validCount = $validCount + 1;
                    }
                });
                //checking count for cards waiting for validation
                if ($validCount > 0) {
                    $lang = session()->has('lang') ? session()->get('lang') : 'FR';
                    $dataMail = $this->getMailData($workshop_data, 'reminder_wkadmin_' . $lang, []);
                    $subject = $dataMail['subject'];
                    $hostname = $this->host->hostname()['fqdn'];
                    $domain = strtok($hostname, '.');
                    if (!empty($hostname))
                        $redirectUrl = env('HOST_TYPE') . $hostname . '/#/qualification/' . $v->id . '/candidates';
                    else
                        $redirectUrl = url('/#/qualification/' . $v->id . '/candidates');
                    $mailData['mail'] = ['subject'           => ($subject), 'emails' => $emails, 'workshop_data' => $workshop_data, 'template_setting' => 'reminder_wkadmin_' . $lang, 'url' => $redirectUrl,
                                         'candidate_fname'   => '',
                                         'candidate_lname'   => '',
                                         'candidate_company' => '', 'date' => '', 'domain' => '',
                    ];
                    $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                }
                //check only when no user of cards waiting for validation found
                if ($validCount == 0) {
                    //checking cards waiting ofr pre-validation user id is related that workshop
                    $pre->whereIn('user_id', $usersId)->map(function ($val, $key) use (&$usersId, &$workshop_data, &$emails, &$registrationService, &$preCount) {
                        if (in_array($val->user_id, $usersId)) {
                            $preCount = $preCount + 1;
                        }
                    });
                    //checking count for cards waiting for pre-validation
                    if ($preCount > 0) {
                        $lang = session()->has('lang') ? session()->get('lang') : 'FR';
                        $dataMail = $this->getMailData($workshop_data, 'reminder_wkadmin_' . $lang, []);
                        $subject = $dataMail['subject'];
                        $hostname = $this->host->hostname()['fqdn'];
                        $domain = strtok($hostname, '.');
                        if (!empty($hostname))
                            $redirectUrl = env('HOST_TYPE') . $hostname . '/#/qualification/' . $v->id . '/candidates';
                        else
                            $redirectUrl = url('/#/qualification/' . $v->id . '/candidates');
                        $mailData['mail'] = ['subject'           => ($subject), 'emails' => $emails, 'workshop_data' => $workshop_data, 'template_setting' => 'reminder_wkadmin_' . $lang, 'url' => $redirectUrl,
                                             'candidate_fname'   => '',
                                             'candidate_lname'   => '',
                                             'candidate_company' => '', 'date' => '', 'domain' => '',
                        ];
                        $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                    }
                }
            });

        }


        /**
         * @param $year
         * this is for testing only
         */
        public function dataUpdateForTesting($year)
        {
            $rejectedCards = UserMeta::where('is_final_save', config('constants.REJECTED_STATUS'))->get(['user_id']);
            $candidates = CandidateCard::/*whereIn('workshop_id', [156])->*/ orderBy('id', 'desc')->whereNotIn('user_id', (count($rejectedCards) > 0) ? $rejectedCards->toArray() : [])->get(['id', 'date_of_validation', 'card_instance', 'user_id']);
            $steps = Step::count();
            $users = $candidates->groupBy('user_id');
            foreach ($users as $user) {
                foreach ($user->sortByDesc('card_instance') as $k => $item) {
                    QualificationUserReminder::where('user_id', $item->user_id)->whereIn('type_of_email', [1, 2, 3, 4, 6])->delete();
                    $item->update(['date_of_validation' => Carbon::today()/*->subDays(4)*/ ->subDays((($k + 1) * $year))->format('Y-m-d')]);
                    ReviewStep::where('user_id', $item->user_id)->where('for_card_instance', $item->card_instance)->update(['saved_for' => Carbon::today()/*->subDays(4)*/ ->subDays((($k + 1) * $year))->format('Y-m-d')]);

                    ReviewStepField::where('user_id', $item->user_id)->where('for_card_instance', $item->card_instance)->update(['saved_for' => Carbon::today()/*->subDays(4)*/ ->subDays((($k + 1) * $year))->format('Y-m-d')]);

                    UserSkill::where('field_id', $item->user_id)->where('type', 'candidate')->update(['created_at' => Carbon::today()/*->subDays(4)*/ ->subDays((($k + 1) * $year))->format('Y-m-d')]);

                }
            }

            $date = Carbon::today()->format('Y-m-d');

            foreach (CandidateCard::/*where('workshop_id', 156)->*/ whereRaw("DATE_ADD(date_of_validation, INTERVAL 365 DAY) <='$date'")->cursor() as $item1) {
                $item1->update(['is_archived' => 1, 'updated_at' => $date]);
            }

//        foreach (CandidateCard::groupBy('user_id')->orderBy('id', 'desc')->where('is_archived', 0)->cursor() as $item2) {
//            UserSkill::where('field_id', $item2->user_id)->where('type', 'candidate')->update(['created_at' => $item2->date_of_validation]);
//        }
        }

        /**
         * @param $months
         */
        public function updateCardArchive($months)
        {
            $date = Carbon::today()->addMonths($months)->format('Y-m-d');
            foreach (CandidateCard::/*where('workshop_id', 156)->*/ where('is_archived', 0)->whereRaw("DATE_ADD(date_of_validation, INTERVAL 365 DAY) <='$date'")->cursor() as $item1) {
                $item1->update(['is_archived' => 1, 'updated_at' => $date]);
            }
        }

        /**
         * @param $year
         * this mail we used to send first and fourth renewal mails
         * using manual script
         */
        public function scriptCronMail($year)
        {
            if (in_array($year, [1, 2, 3, 5, 6, 7]))
                $first = $this->scriptSendFirstRenewalMailCron($year);
            if ($year == 4)
                $fourth = $this->scriptSendFourthRenewalMailCron($year);

            $this->archiveCardCron($year);
            dd(@$first, @$fourth);
        }

        /**
         * @param int $year
         */
        public function scriptSendFirstRenewalMailCron($year = 0)
        {
            //this is for testing only
            if ($year > 0) {
                $date = Carbon::today()->addMonths(10)->addDays(10)->format('Y-m-d');
            } else {
                $date = Carbon::today()->format('Y-m-d');
            }
            $rejectedCards = UserMeta::where('is_final_save', config('constants.REJECTED_STATUS'))->get(['user_id']);

            $cards = CandidateCard::/*whereIn('workshop_id', [402])->*/ whereNotIn('card_instance', [4, 8, 12, 16, 20, 24])->whereNotIn('user_id', (count($rejectedCards) > 0) ? $rejectedCards->toArray() : [])->with('user.userMeta')->whereHas('user.userInfo', function ($q) {
                $q->where('is_final_save', '>', 2);
            })->whereRaw("DATE_ADD(date_of_validation, INTERVAL 306 DAY) <= '$date'")->where('is_archived', 0)->orderBy('id', 'desc')->get(['id', 'user_id', 'card_instance']);

            $emails = [];
            $userReminder = [];
            $cards->unique('user_id')->map(function ($v, $k) use (&$userReminder, $date) {
                if (isset($v->user->email) && isset($v->user->userMeta[0]->workshop_id)) {
                    $latestCard = $v->user->load('userCards');
                    if (isset($latestCard->userCards) && count($latestCard->userCards) > 0) {
                        $card = $latestCard->userCards[(count($latestCard->userCards) - 1)]->card_instance;
                    } else {
                        $card = $v->card_instance;
                    }
                    if (($card == $v->card_instance)) {
                        $emails[] = $v->user->email;
                        $workshop_data = Workshop::withoutGlobalScopes()->with('meta')->find($v->user->userMeta[0]->workshop_id);
                        //check that workshop must be active qualification
                        if (isset($workshop_data->is_qualification_workshop) && $workshop_data->is_qualification_workshop == 1 /*&& $workshop_data->id == 156*/) {
                            $dataMail = $this->getMailData($workshop_data, 'candidate_renewal_1_year_' . (!empty(session()->get('lang')) ? session()->get('lang') : 'FR'), $v->user);
                            $subject = $dataMail['subject'];
                            $hostname = $this->host->hostname()['fqdn'];
                            $domain = strtok($hostname, '.');
                            if (!empty($hostname))
                                $redirectUrl = env('HOST_TYPE') . $hostname . '/#/qualification/registration-form';
                            else
                                $redirectUrl = url('/#/qualification/registration-form');
                            $mailData['mail'] = ['subject'           => ($subject), 'emails' => $emails, 'workshop_data' => $workshop_data, 'template_setting' => 'candidate_renewal_1_year_' . (!empty(session()->get('lang')) ? session()->get('lang') : 'FR'), 'url' => $redirectUrl, 'candidate_fname' => $v->user->fname,
                                                 'candidate_lname'   => $v->user->lname,
                                                 'candidate_company' => '',
                            ];
                            $userReminder[] = ['user_id' => $v->user->id, 'type_of_email' => 1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')];
                            $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                            $this->step->resetUserMeta($v->user->id);

                            //only for testing
                            //$v->update(['date_of_validation' => $date]);
                        }
                    }
                }
            });

            if (count($userReminder) > 0)
                QualificationUserReminder::insert($userReminder);

        }

        /**
         * @param int $year
         */
        public function scriptSendFourthRenewalMailCron($year = 0)
        {
            //this is for testing only
            if ($year > 0) {
                $date = Carbon::today()->addMonths(10)->addDays(10)->format('Y-m-d');
            } else {
                $date = Carbon::today()->format('Y-m-d');
            }
            $rejectedCards = UserMeta::where('is_final_save', config('constants.REJECTED_STATUS'))->get(['user_id']);

            $cards = CandidateCard::/*whereIn('workshop_id', [156])->*/ whereIn('card_instance', [4, 8, 12, 16, 20, 24])->whereNotIn('user_id', (count($rejectedCards) > 0) ? $rejectedCards->toArray() : [])->
            with('user.userMeta')->whereHas('user.userInfo', function ($q) {
                $q->where('is_final_save', '>', 2);
            })->whereRaw("DATE_ADD(date_of_validation, INTERVAL 306 DAY) <= '$date'")->where('is_archived', 0)->orderBy('id', 'desc')->get(['id', 'user_id', 'card_instance']);

            $emails = [];
            $userReminder = [];
            $cards->unique('user_id')->map(function ($v, $k) use (&$userReminder, $date) {

                if (isset($v->user->email) && isset($v->user->userMeta[0]->workshop_id)) {
                    $emails[] = $v->user->email;

                    $workshop_data = Workshop::withoutGlobalScopes()->with('meta')->find($v->user->userMeta[0]->workshop_id);
                    //check that workshop must be active qualification
                    if (isset($workshop_data->is_qualification_workshop) && $workshop_data->is_qualification_workshop == 1 /*&& $workshop_data->id == 156*/) {
                        $dataMail = $this->getMailData($workshop_data, 'candidate_renewal_4_year_' . (!empty(session()->get('lang')) ? session()->get('lang') : 'FR'), $v->user);
                        $subject = $dataMail['subject'];
                        $hostname = $this->host->hostname()['fqdn'];
                        $domain = strtok($hostname, '.');
                        if (!empty($hostname))
                            $redirectUrl = env('HOST_TYPE') . $hostname . '/#/qualification/registration-form';
                        else
                            $redirectUrl = url('/#/qualification/registration-form');
                        $mailData['mail'] = ['subject'           => ($subject), 'emails' => $emails, 'workshop_data' => $workshop_data, 'template_setting' => 'candidate_renewal_4_year_' . (!empty(session()->get('lang')) ? session()->get('lang') : 'FR'), 'url' => $redirectUrl, 'candidate_fname' => $v->user->fname,
                                             'candidate_lname'   => $v->user->lname,
                                             'candidate_company' => '',
                        ];
                        $userReminder[] = ['user_id' => $v->user->id, 'type_of_email' => 4, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')];
                        $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                        $this->step->resetUserMeta($v->user->id);

                        //only for testing
                        //$v->update(['date_of_validation' => $date]);
                    }
                }
            });
            if (count($userReminder) > 0)
                QualificationUserReminder::insert($userReminder);
        }

        /**
         *This function we used to send Expert and Wkadmin Mails
         * using manual script
         */
        public function sendSecDepMails()
        {
            $this->reminderForExpert();
            $this->reminderForWkadmin();
            dd('Script Run');
        }

        /**
         *This function we used to send Reminder of Registration, Renewal and Referrer Mails
         * using manual script
         * in this we are calling 4 reminder functions which are created for mail test
         */
        public function sendAllReminderMails($workshopId = 156)
        {
            //calling registration function and passing workshop id
            $this->reminderScriptRegistration($workshopId);
            //calling First Renewal reminder function and passing workshop id
            $this->reminderScriptRenewalFirst($workshopId);
            //calling Fourth Renewal reminder function and passing workshop id
            $this->reminderScriptRenewalFourth($workshopId);
            //calling reminder Referrer function and passing workshop id
            $this->reminderScriptReferrer($workshopId);
        }

        /**
         * @param $workshopId
         *  $workshopId is used for condition of any specific workshop
         * This function we used to send registration Reminder mail only no conditions
         */
        public function reminderScriptRegistration($workshopId = 156)
        {
            $users = User::where(['on_off' => 1, 'sub_role' => 'C1'])->with('userInfo', 'userMeta', 'userRegistrationReminder', 'userSkillCompany')->withCount('userCards')->whereHas('userInfo', function ($b) {
                $b->where('is_final_save', 0);
            })->get();

            $userReminder = [];
            $registrationService = RegistrationService::getInstance();
            $users->unique('id')->map(function ($v, $k) use (&$userReminder, &$registrationService, $workshopId) {
                if (isset($v->email) && isset($v->userMeta[0]->workshop_id) && $v->user_cards_count == 0) {

                    $emails[] = $v->email;
                    $workshop_data = Workshop::withoutGlobalScopes()->with('meta')->find($v->userMeta[0]->workshop_id);
                    //check that workshop must be active qualification
                    if (isset($workshop_data->is_qualification_workshop) && $workshop_data->is_qualification_workshop == 1 && $workshop_data->id == $workshopId) {
                        $dataMail = $this->getMailData($workshop_data, 'reminder_welcome_email_' . (!empty(session()->get('lang')) ? session()->get('lang') : 'FR'), $v);
                        $subject = $dataMail['subject'];
                        $hostname = $this->host->hostname()['fqdn'];
                        $domain = strtok($hostname, '.');
                        if (!empty($hostname))
                            $redirectUrl = env('HOST_TYPE') . $hostname . '/#/qualification/registration-form';
                        else
                            $redirectUrl = url('/#/qualification/registration-form');
                        $mailData['mail'] = ['subject'           => ($subject), 'emails' => $emails, 'workshop_data' => $workshop_data, 'template_setting' => 'reminder_welcome_email_' . (!empty(session()->get('lang')) ? session()->get('lang') : 'FR'), 'url' => $redirectUrl,
                                             'candidate_fname'   => $v->fname,
                                             'candidate_lname'   => $v->lname,
                                             'candidate_company' => isset($v->userSkillCompany->text_input) ? $v->userSkillCompany->text_input : '', 'date' => $registrationService->getDeliveryDate($v->id), 'domain' => getGrantedDomain($v->id),
                        ];
                        $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                    }
                }
            });
        }

        /**
         * @param $workshopId
         * $workshopId is used for condition of any specific workshop
         * This function we used to send First Renewal Reminder mail only no conditions
         */
        public function reminderScriptRenewalFirst($workshopId = 156)
        {
            $emailsFirstRenewal = QualificationUserReminder::where('type_of_email', 1)->with('user.userMeta', 'user.userFirstReminder', 'user.userSkillCompany')->get();
            //checking users status in meta submitted or not
            $rejectedCards = UserMeta::where('is_final_save', config('constants.REJECTED_STATUS'))->get(['user_id']);

            $userMeta = UserMeta::whereIn('user_id', $emailsFirstRenewal->pluck('user_id'))->where('is_final_save', 0)->whereNotIn('user_id', (count($rejectedCards) > 0) ? $rejectedCards->toArray() : [])->get();
            $emailsFirstRenewal = $emailsFirstRenewal->whereIn('user_id', $userMeta->pluck('user_id'));
            $emails = [];
            $userReminder = [];
            $registrationService = RegistrationService::getInstance();
            $emailsFirstRenewal->unique('user_id')->map(function ($v, $k) use (&$userReminder, &$registrationService, $workshopId) {
                if (isset($v->user->email) && isset($v->user->userMeta[0]->workshop_id)) {
                    $emails[] = $v->user->email;
                    $workshop_data = Workshop::withoutGlobalScopes()->with('meta')->find($v->user->userMeta[0]->workshop_id);
                    //check that workshop must be active qualification
                    if (isset($workshop_data->is_qualification_workshop) && $workshop_data->is_qualification_workshop == 1 && $workshop_data->id == $workshopId) {
                        $dataMail = $this->getMailData($workshop_data, 'reminder_candidate_renewal_1_year_' . (!empty(session()->get('lang')) ? session()->get('lang') : 'FR'), $v->user);

                        $subject = $dataMail['subject'];
                        $hostname = $this->host->hostname()['fqdn'];
                        $domain = strtok($hostname, '.');
                        if (!empty($hostname))
                            $redirectUrl = env('HOST_TYPE') . $hostname . '/#/qualification/registration-form';
                        else
                            $redirectUrl = url('/#/qualification/registration-form');
                        $mailData['mail'] = ['subject'           => ($subject), 'emails' => $emails, 'workshop_data' => $workshop_data, 'template_setting' => 'reminder_candidate_renewal_1_year_' . (!empty(session()->get('lang')) ? session()->get('lang') : 'FR'), 'url' => $redirectUrl,
                                             'candidate_fname'   => $v->user->fname,
                                             'candidate_lname'   => $v->user->lname,
                                             'candidate_company' => isset($v->user->userSkillCompany->text_input) ? $v->user->userSkillCompany->text_input : '', 'date' => $registrationService->getDeliveryDate($v->user->id), 'domain' => getGrantedDomain($v->user->id),
                        ];
                        $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                    }
                }
            });
        }

        /**
         * @param $workshopId
         * $workshopId is used for condition of any specific workshop
         * This function we used to send Fourth Renewal Reminder mail only
         */
        public function reminderScriptRenewalFourth($workshopId = 156)
        {

            $registrationService = RegistrationService::getInstance();
            $emailsFirstRenewal = QualificationUserReminder::where('type_of_email', 4)->with('user.userMeta', 'user.userSkillCompany', 'user.userFourthReminder')->get();
            //checking users status in meta submitted or not
            $rejectedCards = UserMeta::where('is_final_save', config('constants.REJECTED_STATUS'))->get(['user_id']);

            $userMeta = UserMeta::whereIn('user_id', $emailsFirstRenewal->pluck('user_id'))->where('is_final_save', 0)->whereNotIn('user_id', (count($rejectedCards) > 0) ? $rejectedCards->toArray() : [])->get();
            $emailsFirstRenewal = $emailsFirstRenewal->whereIn('user_id', $userMeta->pluck('user_id'));
            $emails = [];
            $userReminder = [];
            $emailsFirstRenewal->unique('user_id')->map(function ($v, $k) use (&$userReminder, &$registrationService, $workshopId) {
                if (isset($v->user->email) && isset($v->user->userMeta[0]->workshop_id)) {
                    $emails[] = $v->user->email;
                    $workshop_data = Workshop::withoutGlobalScopes()->with('meta')->find($v->user->userMeta[0]->workshop_id);
                    //check that workshop must be active qualification
                    if (isset($workshop_data->is_qualification_workshop) && $workshop_data->is_qualification_workshop == 1 && $workshop_data->id == $workshopId) {
                        $dataMail = $this->getMailData($workshop_data, 'reminder_candidate_renewal_4_year_' . (!empty(session()->get('lang')) ? session()->get('lang') : 'FR'), $v->user);
                        $subject = $dataMail['subject'];
                        $hostname = $this->host->hostname()['fqdn'];
                        $domain = strtok($hostname, '.');
                        if (!empty($hostname))
                            $redirectUrl = env('HOST_TYPE') . $hostname . '/#/qualification/registration-form';
                        else
                            $redirectUrl = url('/#/qualification/registration-form');
                        $mailData['mail'] = ['subject'           => ($subject), 'emails' => $emails, 'workshop_data' => $workshop_data, 'template_setting' => 'reminder_candidate_renewal_4_year_' . (!empty(session()->get('lang')) ? session()->get('lang') : 'FR'), 'url' => $redirectUrl,
                                             'candidate_fname'   => $v->user->fname,
                                             'candidate_lname'   => $v->user->lname,
                                             'candidate_company' => isset($v->user->userSkillCompany->text_input) ? $v->user->userSkillCompany->text_input : '', 'date' => $registrationService->getDeliveryDate($v->user->id), 'domain' => getGrantedDomain($v->user->id),
                        ];
                        $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                    }
                }
            });
        }

        /**
         * @param $workshopId
         * $workshopId is used for condition of any specific workshop
         * This function we used to send Referrer  Reminder mail only
         */
        public function reminderScriptReferrer($workshopId = 156)
        {

            $ref = ReferrerField::with('candidate.userMeta', 'candidate', 'referrer.userReffrerReminder')->whereNull('file')->orderByDesc('id')->get();

            $emails = [];
            $userReminder = [];
            $ref->map(function ($v, $k) use (&$userReminder, $workshopId) {
                if (isset($v->referrer->email) && isset($v->candidate->userMeta[0]->workshop_id) && (isset($v->candidate->userInfo->is_final_save) && $v->candidate->userInfo->is_final_save < 3)) {
                    $emails[] = $v->referrer->email;
                    $workshop_data = Workshop::withoutGlobalScopes()->with('meta')->find($v->candidate->userMeta[0]->workshop_id);
                    //check that workshop must be active qualification
                    if (isset($workshop_data->is_qualification_workshop) && $workshop_data->is_qualification_workshop == 1 && $workshop_data->id == $workshopId) {
                        $dataMail = $this->getMailData($workshop_data, 'reminder_magic_link_submit_' . (!empty(session()->get('lang')) ? session()->get('lang') : 'FR'), $v->candidate, $v->referrer->id);
                        $subject = $dataMail['subject'];
                        $hostname = $this->host->hostname()['fqdn'];
                        $domain = strtok($hostname, '.');
                        if (!empty($hostname))
                            $redirectUrl = env('HOST_TYPE') . $hostname . '/referrer/external-referrer-view/' . encrypt($v->id);
                        else
                            $redirectUrl = url('/referrer/external-referrer-view/' . encrypt($v->id));

                        $mailData['mail'] = ['subject'   => ($subject), 'emails' => $emails, 'workshop_data' => $workshop_data, 'template_setting' => 'reminder_magic_link_submit_' . (!empty(session()->get('lang')) ? session()->get('lang') : 'FR'), 'url' => $redirectUrl,
                                             'firstname' => $v->referrer->fname,
                                             'lastname'  => $v->referrer->lname,
                                             'company'   => $v->referrer->company,
                        ];
                        $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                    }
                }
            });
        }

    }

