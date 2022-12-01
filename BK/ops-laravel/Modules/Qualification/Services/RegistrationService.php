<?php
    /**
     * Created by PhpStorm.
     * User: Sourabh Pancharia
     * Date: 6/25/2019
     * Time: 04:14 PM
     */
    
    namespace Modules\Qualification\Services;
    
    use App\Workshop;
    use App\WorkshopMeta;
    use Hyn\Tenancy\Models\Hostname;
    use Modules\Qualification\Entities\CandidateCard;
    use Modules\Qualification\Entities\Prospect;
    use Modules\Qualification\Entities\Domain;
    use Modules\Qualification\Entities\UserDomain;
    use Modules\Qualification\Entities\QualificationClients;
    use App\User;
    use App\Signup;
    use App\Entity;
    use App\EntityUser;
    use Hash;
    use Modules\Qualification\Entities\Step;
    use Illuminate\Http\Request;
    use App\Model\Skill;
    use App\Model\UserMeta;
    use Carbon\Carbon;
    use Auth;
    use DB;
    
    class RegistrationService
    {
        /**
         * SuperAdminSingleton constructor.
         */
        private $contactServices;
        
        public function __construct()
        {
            
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);
            $this->core = app(\App\Http\Controllers\CoreController::class);
            $this->step = app(\Modules\Qualification\Http\Controllers\StepController::class);
            $this->userSkill = app(\App\Http\Controllers\ImproveMentFour\UserSkillController::class);
        }
        
        /**
         * Make instance of SuperAdmin singleton class
         * @return SuperAdmin|null
         */
        public static function getInstance()
        {
            static $instance = NULL;
            if (NULL === $instance) {
                $instance = new static();
            }
            return $instance;
        }
        
        public function getWorkshopData($data)
        {
//            $zipCode = substr(str_replace(' ', '', $data['zip_code']), 0, 2);
            $zipCode = (str_replace(' ', '', $data['zip_code']));
            $getHostname = Hostname::where('fqdn', $data['domain'])->first();
            $host = $this->tenancy->hostname($getHostname);
//         $host=$this->tenancy->hostname($getHostname);
//             dd($zipCode);
            $res['workshop'] = Workshop::where('code1', $zipCode)->where('is_qualification_workshop', '!=', 0)->withoutGlobalScopes()->first();
            if ($res['workshop'] != NULL) {
                if ($res['workshop']->setting != NULL) {
                    $setting = $res['workshop']->setting;
                    // var_dump($setting);die;
                    $res['workshop']->workshop_logo = env('AWS_PATH') . $setting['web']['header_logo'];
                    // $res['workshop']->workshop_logo = 'https://s3-eu-west-2.amazonaws.com/ooionline.com/uploads/eFZorOIB9Mgt3caRVWXw5E1UbjZYCv50ECJHCYN7.png';
                    
                } else {
                    $res['workshop']->workshop_logo = NULL;
                    // $res['workshop']->workshop_logo = 'https://s3-eu-west-2.amazonaws.com/ooionline.com/uploads/eFZorOIB9Mgt3caRVWXw5E1UbjZYCv50ECJHCYN7.png';
                    
                }
            }
            // $res['domain']=Domain::get();
            $step = Step::where('sort_order', 1)->first();
            if ($step) {
                $getAdminStepFields = $this->step->getAdminStepFields($step->id);
                if ($getAdminStepFields->original['status']) {
                    $res['field'] = $getAdminStepFields->original['data'];
                } else {
                    $res['field'] = [];
                }
            } else {
                $res['field'] = [];
            }
            return $res;
        }
        
        public function getStepZeroSkill($userId)
        {
            
            $step = Step::where('sort_order', 1)->first();
            $getAdminStepFields = Step::where('id', $step->id)->with(['fields' => function ($b) {
                $b->select('skills.id', 'skill_tab_id', 'name', 'short_name', 'is_valid', 'is_mandatory', 'skill_format_id', 'is_unique', 'skills.sort_order', 'is_conditional', 'is_qualifying');
            }, 'fields.skillFormat'                                            => function ($a) {
                $a->select('id', 'name_en', 'name_fr');
            }, 'fields.userSkill'                                              => function ($q) use ($userId) {
                $q->where('field_id', $userId)->where('type', 'candidate');
                /*$q->orWhere('user_id', $userId)->orWhere('type', null);*/
            }, 'fields.skillImages', 'fields.skillSelect', 'fields.skillCheckBox', 'fields.skillMeta', 'fields.skillCheckBoxAcceptance'])->first(['id', 'name', 'description', 'is_conditional', 'is_final_step', 'button_text', 'sort_order']);
//            $getAdminStepFields=$this->step->getStepFields($step->id,$userId);
            // $getAdminStepFields=$this->step->getAdminStepFields($step->id);
            $res = $getAdminStepFields;
            return $res;
        }
        
        public function getStepZeroSkillNew($userId, $cardCount = 0)
        {
            
            $domain = Step::orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->where('is_final_step', 1)->with(['domainCheckboxSingle.domainStep.stepReview' => function ($a) use ($userId, $cardCount) {
                $a->where(function ($c) use ($userId, $cardCount) {
                    $c->where('opinion_by', 1);
                    $c->where('user_id', $userId);
                    $c->where('opinion', 0);
                    $c->where('for_card_instance', ($cardCount + 1));
                });
            }])->whereHas('domainCheckboxSingle.domainStep.stepReview', function ($a) use ($userId, $cardCount) {
                $a->where(function ($c) use ($userId, $cardCount) {
                    $c->where('opinion_by', 1);
                    $c->where('user_id', $userId);
                    $c->where('opinion', 0);
                    $c->where('for_card_instance', ($cardCount + 1));
                });
            })->get();
            $getAdminStepFields = Step::orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->where('is_final_step', 1)->get();
            $res['getAdminStepFields'] = $getAdminStepFields;
            $res['domain'] = $domain;
            return $res;
        }
        
        public function getStepfields($userId, $isQualiflec = 0)
        {
            if ($isQualiflec) {
                $typeData = ['Raison sociale', 'Forme juridique', 'SIRET'];
            } else {
                $typeData = ['LEGALFORM', 'CODEAPE', 'QUALITYONE', 'QUALITYTWO', 'QUALITYTHREE', 'QUALITYFOUR', 'QUALITYFIVE', 'QUALITYONEDETAILS', 'QUALITYTWODETAILS', 'QUALITYTHREEDETAILS', 'QUALITYFOURDETAILS', 'QUALITYFIVEDETAILS'];
            }
            
            $getAdminStepFields = Skill::whereIn('short_name', $typeData)->with(['skillFormat' => function ($a) {
                $a->select('id', 'name_en', 'name_fr');
            }, 'userSkill'                                                                     => function ($q) use ($userId) {
                $q->where('field_id', $userId)->where('type', 'candidate')/*->select('id','user_id','skill_id','text_input','field_id','type','created_by')*/
                ;
            }, 'skillImages', 'skillSelect', 'skillCheckBox', 'skillMeta', 'skillCheckBoxAcceptance'])->get(['id', 'skill_tab_id', 'name', 'short_name', 'skill_format_id']);
//         
            $res = [];
            if ($getAdminStepFields != NULL) {
                foreach ($getAdminStepFields as $key => $item) {
                    $res[$item->short_name] = $item;
                }
            }
            // dd($res);
            return $res;
        }
        
        function getDeliveryDate($userId, $instance = 1)
        {
            $lang = session()->has('lang') ? strtoupper(session()->get('lang')) : "FR";
            $user = CandidateCard::where(['user_id' => $userId/*, 'is_archived' => 0*/, 'card_instance' => ($instance + 1)])->orderBy('id', 'desc')->first();
            $data = [];
            if ($user != NULL) {
                if ($lang == 'FR') {
                    setlocale(LC_TIME, 'fr_FR');
                    $date = Carbon::parse($user->date_of_validation)->format('d F Y');
                    $data['deliverydate_orig'] = Carbon::parse($user->date_of_validation)->format('Y');
                    $expdate = Carbon::parse($user->date_of_validation)->addYear(1)->subDay(1)->format('d F Y');
                    $data['expdeliverydate_orig'] = Carbon::parse($user->date_of_validation)->addYear(1)->subDay(1)->format('Y');
                    $date_fr = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'aout', 'septembre', 'octobre', 'novembre', 'décembre'];
                    $dateEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                    $data['deliverydate'] = str_replace($dateEn, $date_fr, $date);
                    $data['expdeliverydate'] = str_replace($dateEn, $date_fr, $expdate);
                    
                } else {
                    $data['deliverydate'] = Carbon::parse($user->date_of_validation)->format('d F Y');
                    $data['deliverydate_orig'] = Carbon::parse($user->date_of_validation)->format('Y');
                    $data['expdeliverydate'] = Carbon::parse($user->date_of_validation)->addYear(1)->subDay(1)->format('d F Y');
                    $data['expdeliverydate_orig'] = Carbon::parse($user->date_of_validation)->addYear(1)->subDay(1)->format('Y');
                    
                }
                
            } else {
                $data['deliverydate'] = NULL;
                $data['expdeliverydate'] = NULL;
            }
            
            return $data;
        }
        
        public function saveFinalData($data)
        {
            
            $workshop = Workshop::with('meta_data')->withoutGlobalScopes()->find($data['workshop_id']);
            if ($data['member_checkbox']) {
                if ($workshop->is_qualification_workshop == 1) {
                    //transaction start
                    DB::connection('tenant')->beginTransaction();
                    $hostname = $this->tenancy->hostname();
                    $hostCode = \DB::connection('mysql')->table('hostname_codes')->where(['fqdn' => $hostname->fqdn])->first(['hash']);
                    $randCode = generateRandomValue(3);
                    $newCode = setPasscode($hostCode->hash, $randCode);
                    $user = User::create([
                        'fname'      => $data['fname'],
                        'lname'      => $data['lname'],
                        'email'      => $data['email'],
                        'password'   => Hash::make($data['email']),
                        'sub_role'   => 'C1',
                        'role'       => 'M2',
                        'on_off'     => 0,
                        'hash_code'  => $newCode['hashCode'],
                        'login_code' => $newCode['userCode'],
                        'phone'      => $data['tel'],
                        'mobile'     => isset($data['mobile']) ? $data['mobile'] : NULL,
                        'postal'     => $data['zip_code']//NULL $data['zip_code']
                    ]);
                    // add him in workshop
                    WorkshopMeta::create(['workshop_id' => $data['workshop_id'], 'user_id' => $user->id, 'role' => 4]);
                    
                    foreach ($data['field'] as $item) {
                        $request = new Request((array)$item);
                        if (isset($request->skill_format_id) && $request->skill_format_id != 20) {
                            $request->merge(['skill_id' => $request->id, 'user_id' => $user->id, 'field_id' => $user->id, 'type' => 'candidate', 'for_card_instance' => 1]);
                            //var_export($request->all());exit;
                            $this->userSkill->addUserSkill($request);
                        }
                    }
                    
                    $code = genRandomNum(6);
                    $lastId = Signup::insertGetId(['email' => $data['email'], 'code' => $code]);
                    if ($data['domain'] == 'localhost') {
                        $redirectUrl = url('qualification/registration-process?email=' . base64_encode($data['email']) . '&userid=' . base64_encode($lastId));
                    } else {
                        $redirectUrl = env('HOST_TYPE') . $data['domain'] . '/qualification/registration-process?email=' . base64_encode($data['email']) . '&userid=' . base64_encode($lastId);
                    }
                    
                    $workshop_data = Workshop::with('meta')->withoutGlobalScopes()->find($data['workshop_id']);
                    $lang = session()->has('lang') ? strtoupper(session()->get('lang')) : "FR";
                    $type = ($lang == 'FR' || $lang == 'fr') ? 'validation_code_FR' : 'validation_code_EN';
                    $dataMail = $this->getMailData($workshop, $type, getCandidateUser($user->id));
                    $subject = $dataMail['subject'];
                    $mailData['mail'] = ['subject' => $subject, 'email' => $data['email'], 'firstname' => $data['fname'], 'otp' => $code, 'path' => $redirectUrl, 'workshop_data' => $workshop_data, 'type' => $type, 'candidateId' => $user->id];
                    if ($this->core->SendEmail($mailData, 'qulification_verification_email')) {
                        
                        DB::connection('tenant')->commit();
                        return ['status' => TRUE, 'code' => 1, 'msg' => 'Email sent', 'type' => 'email', 'redirectUrl' => $redirectUrl];
                    } else {
                        DB::connection('tenant')->rollback();
                        return ['status' => FALSE, 'code' => 1, 'msg' => 'Email not sent', 'type' => 'email', 'redirectUrl' => $redirectUrl];
                    }
                    
                }
                else if ($workshop->is_qualification_workshop == 2) {
                    
                    $insert = [
                        'fname'         => $data['fname'],
                        'lname'         => $data['lname'],
                        'tel'           => $data['tel'],
                        'email'         => $data['email'],
                        'workshop_code' => $workshop->code1,
                        // 'company'=>$data['company'],
                        // 'reg_no' => $data['reg_no'],
                        // 'comment' => $data['comment'],
                        'zip_code'      => $data['zip_code'],
                        'mobile'     => isset($data['mobile']) ? $data['mobile'] : NULL,
                    ];
                    foreach ($data['field'] as $item) {
                        
                        if (isset($item['type']) && $item['type'] == 'company') {
                            $insert['company'] = $item['value'];
                        }
                        if (isset($item['type']) && $item['type'] == 'reg_no') {
                            $insert['reg_no'] = $item['value'];
                        }
                    }
                    $res = QualificationClients::create($insert);
                    $workshop_data = $workshop;
                    $lang = session()->has('lang') ? strtoupper(session()->get('lang')) : "FR";
                    $dataMail = $this->getMailData($workshop_data, 'subscribed_non_installed_' . $lang, $insert);
                    $subject = $dataMail['subject'];
                    $mailData['mail'] = ['subject'           => ($subject), 'emails' => [$data['email']], 'workshop_data' => $workshop_data, 'template_setting' => 'subscribed_non_installed_' . $lang,
                                         'candidate_fname'   => $insert['fname'],
                                         'candidate_lname'   => $insert['lname'],
                                         'candidate_company' => $insert['company'],
                                         'candidate_email'   => $insert['email'],
                                         'candidate_phone'   => $insert['tel'],
                                         'candidate_address' => isset($insert['address']) ? $insert['address'] : '',
                                         'url'               => 'http://www.cartetppro.fr',
                    ];
                    $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                    //mail to wkadmin
                    $wkadmin = [];
                    if (isset($workshop_data->meta)) {
                        $meta = $workshop_data->meta->whereIN('role', [1, 2]);
        
                        $meta->map(function ($v, $k) use (&$wkadmin) {
                            if (isset($v->user->email)) {
                                $wkadmin[] = $v->user->email;
                            }
                        });
                    } else {
                        $wkadmin = [\Illuminate\Support\Facades\Auth::user()->email];
                    }
                    $lang = session()->has('lang') ? strtoupper(session()->get('lang')) : "FR";
                    $dataMail = $this->getMailData($workshop_data, 'new_request_subscriber_non_installed_' . $lang, $insert);
                    $subject = $dataMail['subject'];
                    // $redirectUrl = env('HOST_TYPE') . $data['domain'] .'/#/qualification/' . $workshop_data->id . '/reservoirs';
                    $redirectUrl = env('HOST_TYPE') . $data['domain'] . '/redirect-url?url=' . str_rot13("qualification/$workshop_data->id/reservoirs");
                    $mailData['mail'] = ['subject'           => ($subject),
                                         'emails'            => $wkadmin,
                                         'workshop_data'     => $workshop_data, 'template_setting' => 'new_request_subscriber_non_installed_' . $lang,
                                         'candidate_fname'   => $insert['fname'],
                                         'candidate_lname'   => $insert['lname'],
                                         'candidate_company' => $insert['company'],
                                         'candidate_email'   => $insert['email'],
                                         'candidate_phone'   => $insert['tel'],
                                         'candidate_address' => isset($insert['address']) ? $insert['address'] : '',
                                         'url'               => $redirectUrl,
                    ];
                    $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                    if ($res) {
                        return ['status' => TRUE, 'code' => 2, 'msg' => 'Data save', 'type' => 'data'];
                    }
                    return ['status' => FALSE, 'code' => 2, 'msg' => 'Data not save', 'type' => 'data'];
                }
            } else {
              
                if ($workshop->is_qualification_workshop == 1) {
                    
                    $insert = [
                        'fname'         => $data['fname'],
                        'lname'         => $data['lname'],
                        'tel'           => $data['tel'],
                        'email'         => $data['email'],
                        'workshop_code' => $workshop->code1,
                        // 'company'=>$data['company'],
                        // 'reg_no' => $data['reg_no'],
                        // 'comment' => $data['comment'],
                        'zip_code'      => $data['zip_code'],
                        'case'          => 3,
                        'mobile'     => isset($data['mobile']) ? $data['mobile'] : NULL,
                    ];
                    foreach ($data['field'] as $item) {
                        // var_dump($item);die;
                        if (isset($item['type']) && $item['type'] == 'company') {
                            $insert['company'] = $item['value'];
                        }
                        if (isset($item['type']) && $item['type'] == 'reg_no') {
                            $insert['reg_no'] = $item['value'];
                        }
                    }
                    $res = Prospect::create($insert);
                    
                    $workshop_data = $workshop;
                    $lang = session()->has('lang') ? strtoupper(session()->get('lang')) : "FR";
                    $dataMail = $this->getMailData($workshop_data, 'non_subscribed_installed_' . $lang, $insert);
                    $subject = $dataMail['subject'];
                    $mailData['mail'] = ['subject'           => ($subject), 'emails' => [$data['email']], 'workshop_data' => $workshop_data, 'template_setting' => 'non_subscribed_installed_' . $lang,
                                         'candidate_fname'   => $insert['fname'],
                                         'candidate_lname'   => $insert['lname'],
                                         'candidate_company' => $insert['company'],
                                         'candidate_email'   => $insert['email'],
                                         'candidate_phone'   => $insert['tel'],
                                         'candidate_address' => isset($insert['address']) ? $insert['address'] : '',
                                         'url'               => 'http://www.cartetppro.fr',
                    ];
                    
                    $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                    
                    //mail to wkadmin
                    $lang = session()->has('lang') ? strtoupper(session()->get('lang')) : "FR";
                    $wkadmin = [];
                    if (isset($workshop_data->meta)) {
                        $meta = $workshop_data->meta->whereIN('role', [1, 2]);
        
                        $meta->map(function ($v, $k) use (&$wkadmin) {
                            if (isset($v->user->email)) {
                                $wkadmin[] = $v->user->email;
                            }
                        });
                    } else {
                        $wkadmin = [\Illuminate\Support\Facades\Auth::user()->email];
                    }
                    $dataMail = $this->getMailData($workshop_data, 'new_request_non_subscriber_installed_' . $lang, $insert);
                    $subject = $dataMail['subject'];
                    $getHostname = Hostname::where('fqdn', $data['domain'])->first();
                    $host = $this->tenancy->hostname($getHostname);
                    // $redirectUrl = env('HOST_TYPE') . $data['domain'] .'/#/qualification/' . $workshop_data->id . '/reservoirs';
                    $redirectUrl = env('HOST_TYPE') . $data['domain'] . '/redirect-url?url=' . str_rot13("qualification/$workshop_data->id/reservoirs");
                    // var_dump($redirectUrl);die;
                    $mailData['mail'] = ['subject'           => ($subject), 'emails' => $wkadmin, 'workshop_data' => $workshop_data, 'template_setting' => 'new_request_non_subscriber_installed_' . $lang,
                                         'candidate_fname'   => $insert['fname'],
                                         'candidate_lname'   => $insert['lname'],
                                         'candidate_company' => $insert['company'],
                                         'candidate_email'   => $insert['email'],
                                         'candidate_phone'   => $insert['tel'],
                                         'candidate_address' => isset($insert['address']) ? $insert['address'] : '',
                                         'url'               => $redirectUrl,
                    ];
                    $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                    
                    if ($res) {
                        return ['status' => TRUE, 'code' => 3, 'msg' => 'Data save', 'type' => 'data'];
                    }
                    return ['status' => FALSE, 'code' => 3, 'msg' => 'Data not save', 'type' => 'data'];
                } else if ($workshop->is_qualification_workshop == 2) {
                    
                    
                    $insert = [
                        'fname'         => $data['fname'],
                        'lname'         => $data['lname'],
                        'tel'           => $data['tel'],
                        'email'         => $data['email'],
                        'workshop_code' => $workshop->code1,
                        // 'company'=>$data['company'],
                        // 'reg_no' => $data['reg_no'],
                        // 'comment' => $data['comment'],
                        'zip_code'      => $data['zip_code'],
                        'case'          => 4,
                        'mobile'     => isset($data['mobile']) ? $data['mobile'] : NULL,
                    ];
                    foreach ($data['field'] as $item) {
                        if (isset($item['type']) && $item['type'] == 'company') {
                            $insert['company'] = $item['value'];
                        }
                        if (isset($item['type']) && $item['type'] == 'reg_no') {
                            $insert['reg_no'] = $item['value'];
                        }
                    }
                    $res = Prospect::create($insert);
                    $workshop_data = $workshop;
                    $lang = session()->has('lang') ? strtoupper(session()->get('lang')) : "FR";
                    $dataMail = $this->getMailData($workshop_data, 'non_subscribed_non_installed_' . $lang, $insert);
                    $subject = $dataMail['subject'];
                    $mailData['mail'] = ['subject'           => ($subject), 'emails' => [$data['email']], 'workshop_data' => $workshop_data, 'template_setting' => 'non_subscribed_non_installed_' . $lang,
                                         'candidate_fname'   => $insert['fname'],
                                         'candidate_lname'   => $insert['lname'],
                                         'candidate_company' => $insert['company'],
                                         'candidate_email'   => $insert['email'],
                                         'candidate_phone'   => $insert['tel'],
                                         'candidate_address' => isset($insert['address']) ? $insert['address'] : '',
                                         'url'               => 'http://www.cartetppro.fr',
                    ];
                    $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                    
                    //mail to wkadmin
                    $lang = session()->has('lang') ? strtoupper(session()->get('lang')) : "FR";
                    $wkadmin = [];
                    if (isset($workshop_data->meta)) {
                        $meta = $workshop_data->meta->whereIN('role', [1, 2]);
        
                        $meta->map(function ($v, $k) use (&$wkadmin) {
                            if (isset($v->user->email)) {
                                $wkadmin[] = $v->user->email;
                            }
                        });
                    } else {
                        $wkadmin = [\Illuminate\Support\Facades\Auth::user()->email];
                    }
                    $dataMail = $this->getMailData($workshop_data, 'new_request_non_subscriber_non_installed_' . $lang, $insert);
                    $subject = $dataMail['subject'];
                    
                    // $redirectUrl = env('HOST_TYPE') . $data['domain'] .'/#/qualification/' . $workshop_data->id . '/reservoirs';
                    $redirectUrl = env('HOST_TYPE') . $data['domain'] . '/redirect-url?url=' . str_rot13("qualification/$workshop_data->id/reservoirs");
                    $mailData['mail'] = ['subject'           => ($subject), 'emails' => $wkadmin, 'workshop_data' => $workshop_data, 'template_setting' => 'new_request_non_subscriber_non_installed_' . $lang,
                                         'candidate_fname'   => $insert['fname'],
                                         'candidate_lname'   => $insert['lname'],
                                         'candidate_company' => $insert['company'],
                                         'candidate_email'   => $insert['email'],
                                         'candidate_phone'   => $insert['tel'],
                                         'candidate_address' => isset($insert['address']) ? $insert['address'] : '',
                                         'url'               => $redirectUrl,
                    ];
                    $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                    
                    if ($res) {
                        return ['status' => TRUE, 'code' => 4, 'msg' => 'Data save', 'type' => 'data'];
                    }
                    return ['status' => TRUE, 'code' => 4, 'msg' => 'Data not save', 'type' => 'data'];
                }
            }
        }
        
        public function checkCode($data)
        {
            $res = Signup::where('id', $data['id'])->where('email', $data['email'])->where('code', $data['code']);
            if ($res->first()) {
                $user = User::where('email', $data['email'])->first();
                // dd($user);
                
                if (isset($user->id)) {
                    $user->on_off = 1;
                    $user->save();
                    $workshop = WorkshopMeta::where(['user_id' => $user->id, 'role' => 4])->withoutGlobalScopes()->first();
                
                    if (Auth::loginUsingId($user->id) && isset($workshop->workshop_id)) {
                        
                        $lang = session()->has('lang') ? strtoupper(session()->get('lang')) : "FR";
                        $workshop_data = Workshop::with('meta')->withoutGlobalScopes()->find($workshop->workshop_id);
                        $dataMail = $this->getMailData($workshop_data, 'welcome_email_' . userLang(), getCandidateUser($user->id));
//                    $dataMail = $this->getMailData($workshop_data, 'qulification_welcome_workshop');
                        $subject = $dataMail['subject'];
                        $route_members = $dataMail['route_members'];
                        $mailData['mail'] = ['subject' => ($subject), 'emails' => [$user->email], 'workshop_data' => $workshop_data, 'url' => $route_members, 'template_setting' => 'welcome_email_' . $lang, 'candidateId' => $user->id];
                        $this->core->SendMassEmail($mailData, 'dynamic_workshop_template', $user->toArray());
                        //mail to wkadmin
//                        $wkadmin = isset($workshop_data->meta) ? $workshop_data->meta->where('role', 1)->first() : Auth::user()->email;
                        $wkadmin = [];
                        if (isset($workshop_data->meta)) {
                            $meta = $workshop_data->meta->whereIN('role', [1, 2]);
        
                            $meta->map(function ($v, $k) use (&$wkadmin) {
                                if (isset($v->user->email)) {
                                    $wkadmin[] = $v->user->email;
                                }
                            });
                        } else {
                            $wkadmin = [Auth::user()->email];
                        }
                        
                        $redirectUrl = route('redirect-app-url', ['url' => str_rot13('qualification/' . $workshop_data->id . '/candidates')]);
                        
                        $dataMail = $this->getMailData($workshop_data, 'new_request_initiated_' . $lang, getCandidateUser($user->id));
                        $subject = $dataMail['subject'];
                        $mailData['mail'] = ['subject' => ($subject), 'emails' => $wkadmin, 'workshop_data' => $workshop_data, 'template_setting' => 'new_request_initiated_' . $lang, 'candidateId' => $user->id, 'url' => $redirectUrl];
                        $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
//                  $this->core->SendMassEmail($mailData, 'candidate_commission_new_user');
                        $res->delete();
                        return ['status' => TRUE, 'msg' => '', 'url' => 'change-password']; return ['status' => TRUE, 'msg' => '', 'url' => route('change-password')];
//                return route('change-password');
                    }
                    return ['status' => FALSE, 'msg' => __('message.verification_code')];
                } else {
                    return ['status' => FALSE, 'msg' => __('message.verification_code')];
                }
            } else {
                return ['status' => FALSE, 'msg' => __('message.verification_code')];
            }
        }
        
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
                // $member['v']['fullname'],
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
    }
