<?php

    namespace Modules\Qualification\Http\Controllers;

    use App\Model\UserSkill;
    use File;
    use App;
    use App\Model\UserMeta;
    use App\Signup;
    use App\Workshop;
    use App\User;
    use App\WorkshopMeta;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Routing\Controller;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Crypt;
    use Illuminate\Support\Facades\DB;
    use Modules\Qualification\Entities\Referrer;
    use Modules\Qualification\Entities\ReferrerField;

    use Modules\Qualification\Entities\Step;
    use Modules\Qualification\Entities\Field;
    use App\Model\Skill;
    use Modules\Qualification\Services\StepService;
    use Validator;

    /**
     * Class ReferrerController
     * @package Modules\Qualification\Http\Controllers
     */
    class ReferrerController extends Controller
    {
        /**
         * ReferrerController constructor.
         */
        public function __construct()
        {
            $this->core = app(\App\Http\Controllers\CoreController::class);
            $this->userSkill = app(\App\Http\Controllers\ImproveMentFour\UserSkillController::class);
            // set locale for localization
            App::setLocale((strtolower(session()->get('lang'))) ? strtolower(session()->get('lang')) : 'fr');
        }

        /**
         * Display a listing of the resource.
         * @return Response
         */
        public function index()
        {
            return view('qualification::index');
        }

        /**
         * Show the form for creating a new resource.
         * @return Response
         */
        public function create()
        {
            return view('qualification::create');
        }

        /**
         * Store a newly created resource in storage.
         * @param Request $request
         * @return Response
         */
        public function store(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'fname'         => 'required|regex:/[a-zA-Z0-9\s]+/|min:3|max:255',
                    'lname'         => 'required|regex:/[a-zA-Z0-9\s]+/|min:3|max:255',
                    'email'         => 'required|email',
                    'company'       => 'required|regex:/[a-zA-Z0-9\s]+/|min:3|max:255',
                    'position'      => 'required',
                    'referrer_type' => 'required',
                    // 'field_id' => 'required',
                    'candidate_id'  => 'required',
                    'cardCount'     => 'required',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                // Get current Hostname
                $hostname = app(\Hyn\Tenancy\Environment::class)->hostname();
                //transaction start
                DB::connection('tenant')->beginTransaction();

                $referrer = Referrer::create([
                    'fname'         => $request->fname,
                    'lname'         => $request->lname,
                    'email'         => $request->email,
                    'company'       => $request->company,
                    'position'      => $request->position,
                    'referrer_type' => ($request->referrer_type),
                    'address'       => isset($request->address) ? $request->address : '',
                    'phone'         => isset($request->phone) ? $request->phone : '',
//                'mobile'=>isset($request->mobile)??'',
//                'zip_code'=>isset($request->zip_code)??'',
//                'city'=>isset($request->city)??'',
//                'country'=>isset($request->country)??'',
//                'state'=>isset($request->state)??'',
                ]);

                // if ($request->field_id) {
                $referrerField = ReferrerField::create([
                    'step_id'           => isset($request->step_id) ? $request->step_id : 1,
                    'field_id'          => isset($request->field_id) ? $request->field_id : NULL,
                    'refreer_id'        => $referrer->id,
                    'candidate_id'      => $request->candidate_id,
                    'for_card_instance' => ($request->cardCount + 1),
                ]);
                // }
                if (isset($request->is_domain)) {
                    $redirectUrl = env('HOST_TYPE') . $hostname->fqdn . '/referrer/external-referrer-view/' . encrypt($referrerField->id);
                    $request = new Request($request->all());
                    $request->merge(['step_id' => $request->step_id, 'candidate_id' => $request->candidate_id, 'card_count' => $request->cardCount]);
                    $fieldData = $this->getStepReferrerList($request);
                } else {
                    $redirectUrl = env('HOST_TYPE') . $hostname->fqdn . '/qualification/referrer-link/' . encrypt($referrerField->id);
                }
                $mailData = ['url' => $redirectUrl];
                $mailData['mail']['email'] = $request->email;//'sourabh@sharabh.com'

                $lang = session()->has('lang') ? session()->get('lang') : "FR";
                $sendMail = $this->sendMails($referrer->id, 'referrer_new_request_' . $lang, $mailData, $request->candidate_id);
//            $sendMail = ['status' => true];
                //transaction commit
                //if mail is not send then it is return false otherwise true

                // DB::connection('tenant')->commit();
                // return response()->json(['status' => true, 'msg' => 'Record Added Successfully', 'data' => $mailData], 200);
                if ($sendMail['status']) {
                    DB::connection('tenant')->commit();
                    if (isset($fieldData)) {
                        return $fieldData;
                    }
                    return response()->json(['status' => TRUE, 'msg' => 'Record Added Successfully', 'data' => $referrer], 200);
                } else {
                    DB::connection('tenant')->rollback();
                    return response()->json(['status' => FALSE, 'msg' => $sendMail['msg'], 'data' => $mailData], 200);
                }
            } catch (\Exception $e) {
                //transaction rollback
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * Show the specified resource.
         * @return Response
         */
        public function show()
        {
            return view('qualification::show');
        }

        /**
         * Show the form for editing the specified resource.
         * @return Response
         */
        public function edit()
        {
            return view('qualification::edit');
        }

        /**
         * Update the specified resource in storage.
         * @param Request $request
         * @return Response
         */
        public function update(Request $request)
        {
        }

        /**
         * Remove the specified resource from storage.
         * @return Response
         */
        public function destroy()
        {
        }

        /**
         * @param $refId
         * @param $templateType
         * @param $mailData
         * @param null $candidate
         * @return array
         */
        public function sendMails($refId, $templateType, $mailData, $candidate = NULL)
        {
            if (!empty($candidate)) {
                $meta = WorkshopMeta::where('user_id', $candidate)->first(['workshop_id']);
                $mailData['mail']['workshop_data'] = Workshop::with('meta_data')->withoutGlobalScopes()->find($meta->workshop_id);
            } else {
                $mailData['mail']['workshop_data'] = [];
            }

            $candidateUser = User::find($candidate);
            $getMailData = $this->getMailData($refId, $templateType, $candidateUser, $mailData['mail']['workshop_data']);
            $finalMailData['mail'] = $mailData;
            if (isset($mailData['url'])) {
                $mailData['mail']['url'] = $mailData['url'];
            }
            $mailData['mail']['subject'] = $getMailData['subject'];
            $mailData['mail']['email'] = $finalMailData['mail']['mail']['email'];
            $mailData['mail']['firstname'] = $getMailData['fname'];
            $mailData['mail']['lastname'] = $getMailData['lname'];
            $mailData['mail']['company'] = $getMailData['company'];
            $mailData['mail']['domainOfMagicLink'] = $getMailData['domain'];
            $mailData['mail']['type'] = $templateType;
            $mailData['mail']['template_setting'] = $templateType;
            $mailData['mail']['candidateId'] = $candidate;
            // var_dump($mailData['mail']);die;
            if ($this->core->SendEmail($mailData, 'dynamic_workshop_template')) {
                return ['status' => TRUE, 'msg' => 'Email sent'];
            } else {
                return ['status' => FALSE, 'msg' => 'Email not sent'];
            }
        }

        /**
         * @param int $refId
         * @param $key
         * @param array $candidate
         * @param array $workshop_data
         * @return array
         */
        function getMailData($refId = 0, $key, $candidate = [], $workshop_data = [])
        {
            $ref = Referrer::find($refId);
            $referrerField = ReferrerField::with(['step' => function ($a) {
                $a->select('id', 'name');
            }])->where('id', $ref->id)->first();
            if ($ref != NULL) {
                $currUserFname = $ref->fname;
                $currUserLname = $ref->lname;
                $currUserEmail = $ref->email;
                $currUserCompany = $ref->company;
                $currUserDomain = isset($referrerField->step->name) ? $referrerField->step->name : '-';
            } else {
                $currUserFname = '';
                $currUserLname = '';
                $currUserEmail = '';
                $currUserCompany = '';
            }
            if ($currUserDomain == '-') {
                $getAdminStep = Skill::where('skill_format_id', 20)->get()->pluck('id');
                $getAdminStepFields = Field::whereIn('field_id', $getAdminStep)->with('step')->first();
                $currUserDomain = isset($getAdminStepFields->step->name) ? $getAdminStepFields->step->name : '-';
            }
            // var_dump($currUserDomain);die;
            $settings = getSettingData($key);
            $getOrgDetail = getOrgDetail();
            // dd($getOrgDetail);
            $member = workshopValidatorPresident($workshop_data);
            $wsetting = getWorkshopSettingData($workshop_data->id);
            $WorkshopSignatory = getWorkshopSignatoryData($workshop_data->id);
            $data = [];
            if ($candidate) {
                $data = getCandidateUser($candidate->id);
            }
            $keywords = [
                '[[WorkshopLongName]]',
                '[[WorkshopShortName]]',
                '[[WorkshopPresidentFullName]]',
                // '[[WorkshopvalidatorFullName]]',
                '[[UserFirsrName]]',
                '[[UserLastName]]',
                '[[UserEmail]]',
                '[[OrgName]]',
                '[[candidateFN]]',
                '[[candidateLN]]',
                '[[candidateCompanyName]]',
                '[[candidateEmail]]',
                '[[candidatePhone]]',
                '[[CandidateAddress]]',
                '[[referreeFN]]',
                '[[referreeLN]]',
                '[[referreeCompanyName]]',
                '[[DomainOfTheMagicLink]]',
                '[[SignatoryFname]]',
                '[[Signatorylname]]',
                '[[SignatoryPossition]]',
                '[[SignatoryEmail]]',
                '[[SignatoryPhone]]',
                '[[SignatoryMobile]]',
            ];
            $values = [
                $workshop_data->workshop_name,
                $workshop_data->code1,
                $member['p']['fullname'],
                // $member['v']['fullname'],
                $currUserFname,
                $currUserLname,
                $currUserEmail,
                $getOrgDetail->name_org,
                isset($candidate->fname) ? $candidate->fname : '',
                isset($candidate->lname) ? $candidate->lname : '',
                isset($data->userSkillCompany->text_input) ? $data->userSkillCompany->text_input : '',
                isset($candidate->email) ? $candidate->email : '',
                isset($candidate->phone) ? $candidate->phone : '',
                isset($candidate->address) ? $candidate->address : '',
                $currUserFname,
                $currUserLname,
                // $currUserEmail,
                $currUserCompany,
                $currUserDomain,
                isset($WorkshopSignatory['signatory_fname']) ? $WorkshopSignatory['signatory_fname'] : '',
                isset($WorkshopSignatory['signatory_lname']) ? $WorkshopSignatory['signatory_lname'] : '',
                isset($WorkshopSignatory['signatory_possition']) ? $WorkshopSignatory['signatory_possition'] : '',
                isset($WorkshopSignatory['signatory_email']) ? $WorkshopSignatory['signatory_email'] : '',
                isset($WorkshopSignatory['signatory_phone']) ? $WorkshopSignatory['signatory_phone'] : '',
                isset($WorkshopSignatory['signatory_mobile']) ? $WorkshopSignatory['signatory_mobile'] : '',
            ];

            $subject = (str_replace($keywords, $values, $settings->email_subject));
            return ['subject' => $subject, 'fname' => $currUserFname, 'lname' => $currUserLname, 'email' => $currUserEmail, 'company' => $currUserCompany, 'domain' => $currUserDomain];
        }

        /**
         * @param $data
         * @return $this
         */
        public function link($data)
        {
            try {

                $signup = ReferrerField::where(['id' => decrypt($data)])->orderByDesc('id')->first();

                if ($signup) {
                    //                $ref = Referrer::where(['email' => $arrayData[2]])->first();
                    //                if ($ref != null /*&& $ref->pdf_upload == 0*/) {
                    if ($signup->status == 1) {
                        $referrerField = ReferrerField::with('candidate.userSkillCompany', 'referrer')->with(['domain.step' => function ($a) {
                            $a->select('id', 'name');
                        }])->where('id', $signup->id)->first();

                        $meta = WorkshopMeta::where('user_id', $referrerField->candidate_id)->first(['workshop_id']);
                        $referrer1 = Workshop::withoutGlobalScopes()->find($meta->workshop_id);

                        $field = Step::where('sort_order', 1)->first();
                        $getAdminStepFields = Step::where('id', $field->id)->with(['fields' => function ($b) {
                            $b->select('skills.id', 'skills.skill_tab_id', 'skills.name', 'skills.short_name', 'skills.is_valid', 'skills.is_mandatory', 'skills.skill_format_id', 'skills.is_unique', 'skills.sort_order', 'skills.is_conditional', 'skills.is_qualifying');
                        }, 'fields.skillFormat'                                             => function ($a) {
                            $a->select('id', 'name_en', 'name_fr');
                        }, 'fields.userSkill'                                               => function ($q) use ($referrerField) {
                            $q->where('field_id', $referrerField->candidate_id)->where('type', 'candidate');
                        }, 'fields.skillImages', 'fields.skillSelect', 'fields.skillCheckBox', 'fields.skillMeta', 'fields.skillCheckBoxAcceptance'])->first(['id', 'name', 'description', 'is_conditional', 'is_final_step', 'button_text', 'sort_order']);
                        // dd($getAdminStepFields,$arrayData[1],$referrerField);
                        $lang = getUserLang($referrerField->candidate_id);
                        return view('qualification::candidate')->with(compact('referrerField', 'referrer1', 'field', 'getAdminStepFields', 'lang'));
                    } else {
                        $error = 'The link you are using is not good please use right link.';
                        return view('qualification::candidate')->with(compact('error'));
                        return response()->json(['status' => FALSE, 'msg' => 'url expire'], 200);
                    }
                } else {
                    $error = 'The link you are using is invalid please use valid link.';
                    return view('qualification::candidate')->with(compact('error'));
                    return response()->json(['status' => FALSE, 'msg' => 'Invalid url'], 200);
                }
            } catch (\Exception $e) {
                $error = 'Something wrong happens.';
                return view('qualification::candidate')->with(compact('error'));
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * @param $candidateId
         * @param $fieldId
         * @param $forCard
         * @return mixed
         */
        public function getReferrerData($candidateId, $fieldId, $forCard)
        {
            try {
                $data = ReferrerField::with('referrer:id,fname,lname,email,company,position,referrer_type,phone,address')->where(['candidate_id' => $candidateId, 'field_id' => $fieldId, 'for_card_instance' => ($forCard + 1)])->first(['id', 'field_id', 'refreer_id', 'candidate_id']);
                return response()->json(['status' => TRUE, 'msg' => '', 'data' => $data], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * @param Request $request
         * @return $this|\Illuminate\Http\RedirectResponse
         */
        public function uploadFile(Request $request)
        {
            try {
                $data['field'] = [];
                $referrerFieldData = ReferrerField::with('candidate', 'field', 'domain.step')->where(['id' => $request->id])->first();

                $ref = $request['id'];
                unset($request['id']);
                $request = new Request($request->all());
                $request->merge([
                    "field_id"        => $referrerFieldData->candidate->id,
                    "skill_id"        => $referrerFieldData->field->field_id,
                    "skill_format_id" => 20,
                    "type"            => 'candidate',
                    "value"           => $request->file,
                ]);

                //
                if (isset($referrerFieldData->candidate->email)) {
                    $userSkill = $this->userSkill->addUserSkill($request);
                    if (isset($userSkill->field_id)) {
                        $referrer = Referrer::where('id', $referrerFieldData->refreer_id)->first();
                        $referrer->update(['pdf_upload' => 1]);
                        $referrerFieldData->update(['status' => 0]);
                        //   $signup = Signup::where(['email' => $referrer->email])->delete();
                        $lang = session()->has('lang') ? session()->get('lang') : "FR";
                        $mailData['mail']['email'] = $referrer->email;
                        $mailData['url'] = 'http://www.cartetppro.fr/';
                        $mailData['mail']['type'] = 'referrer_magic_link_submit_' . $lang;
                        $mailData['mail']['template_setting'] = 'referrer_magic_link_submit_' . $lang;
                        $this->sendMails($referrer->id, ('referrer_magic_link_submit_' . $lang), $mailData, $referrerFieldData->candidate->id);
                        // $this->core->SendEmail($mailData, 'dynamic_workshop_template');
                        //============================Candidate
                        $mailData['mail']['email'] = $referrerFieldData->candidate->email;
                        $mailData['mail']['type'] = 'magic_link_received_' . $lang;
                        $mailData['mail']['template_setting'] = 'magic_link_received_' . $lang;
                        $redirectUrl = url('/#/qualification/registration-form');
                        $mailData['url'] = $redirectUrl;
                        //updating candidate current step id
                        UserMeta::where('user_id', $referrerFieldData->candidate->id)->update(['current_step_id' => isset($referrerFieldData->domain->step->id) ? $referrerFieldData->domain->step->id : getFirstStepId()]);
                        $this->sendMails($referrer->id, ('magic_link_received_' . $lang), $mailData, $referrerFieldData->candidate->id);
//                $this->core->SendEmail($mailData, 'dynamic_workshop_template');
                        return redirect()->route('thanks');
                    } else {
                        return redirect()->back()->withInput();
                    }
                } else {
                    return response()->json(['status' => FALSE, 'msg' => 'Candidate Not Found'], 500);
                }


            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function thanksPage()
        {
            return view('qualification::candidate');
        }

        /**
         * @param Request $request
         * @return mixed
         */
        public function ReferrerLink(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'token' => 'required|',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                //
                $host = app(\Hyn\Tenancy\Environment::class)->hostname();
                $qualification_qualifelec_allowed = (in_array((isset($host->id) ? $host->id : 0), [1, 2]) ? 1 : 0);

                $signup = ReferrerField::with('candidate.userInfo', 'candidate.userSkillCompany', 'step', 'referrer')->where(['id' => decrypt($request->token)])/*->whereNull('uploaded_on')*/ ->orderByDesc('id')->first();
                $referrer = Referrer::where('id', $signup->refreer_id)->first(['fname', 'id', 'lname', 'email', 'company', 'position', 'referrer_type', 'address', 'phone']);
                $meta = WorkshopMeta::where('user_id', $signup->candidate_id)->first(['workshop_id']);
                $workshop = Workshop::withoutGlobalScopes()->find($meta->workshop_id);
                if ((isset($workshop->setting['custom_graphics_enable']) && $workshop->setting['custom_graphics_enable'] == 1)) {
                    $wSetting = $workshop->setting;
                } else {
                    $wSetting['web'] = getSettingData('graphic_config', 1);
                }
                if ($signup && empty($signup->uploaded_on) && (isset($signup->candidate->userInfo->is_final_save) && $signup->candidate->userInfo->is_final_save < 3)) {

                    $allSkills = Skill::with(['conditionalSkill', 'userSkill' => function ($b) use ($referrer) {
                        $b->where(['field_id' => $referrer->id, 'type' => 'referrer']);
                    }, 'skillImages', 'skillSelect', 'skillCheckBox', 'skillMeta', 'skillCheckBoxAcceptance'])->whereHas('skillTab', function ($a) {
                        $a->where('tab_type', 6)->whereRaw('JSON_CONTAINS(visible, \'{"referrer": 1}\')');
//                        $a->where('tab_type', 6)->where('visible->referrer',1);
                    })->where('is_valid', 1)->orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->get();
                    //['id', 'skill_tab_id', 'is_valid', 'is_mandatory', 'is_unique', 'sort_order']s
                    $domain = Step::where('is_final_step', 1)->with('domainCheckbox.userSkill')->whereHas('domainCheckbox.userSkill', function ($a) use ($signup) {
                        $a->where(['field_id' => $signup->candidate_id, 'type' => 'candidate']);
                    })->get();
                    $fields = [];
                    //=============//
                    foreach ($allSkills as $item) {

                        if (isset($item->conditionalSkill)) {
                            //getting conditional Skill Data with User
                            $skill = UserSkill::where('field_id', $referrer->id)->where('skill_id', $item->conditionalSkill->conditional_checkbox_id)->first(['id', 'checkbox_input', 'skill_id']);
                            //checking conditional step have conditional skill should checked condition
                            if ($item->conditionalSkill->is_checked) {
                                //checking if skill is check then show data
                                if (isset($skill->checkbox_input) && $skill->checkbox_input) {
                                    $fields[] = $item;
                                } /*else {

                                }*/
                            } else {
                                if (isset($skill->checkbox_input) && $skill->checkbox_input == 0) {
                                    $fields[] = $item;
                                } elseif (!isset($skill->checkbox_input)) {
                                    $fields[] = $item;
                                }
                            }
                        } else {

                            $fields[] = $item;
                        }
                    }
                    // $steps->fields = $fields;
                    $lang = getUserLang($signup->candidate_id);
                    if (isset($signup->candidate->userInfo) && isset($signup->candidate->id)) {
                        $service = StepService::getInstance();
                        $dateOfVal = $service->dateOfValidation($signup->candidate->userInfo, $signup->candidate->id);
                        $date['validation_year'] = Carbon::parse($dateOfVal)->year;
                        $date['expiry_year'] = Carbon::parse($dateOfVal)->addYear(1)->subDay(1)->year;
                    } else {
                        $today = Carbon::today();
                        $date['validation_year'] = $today->year;
                        $date['expiry_year'] = Carbon::parse($today)->addYear(1)->subDay(1)->year;
                    }

                    return response()->json(['status' => TRUE, 'msg' => ['basic' => $referrer, 'custom' => $fields, 'candidate' => $signup->candidate, 'domain' => isset($signup->step->name) ? $signup->step->name : '', 'workshop' => $wSetting, 'lang' => $lang, 'selected_domain' => $domain, 'qualification_qualifelec_allowed' => $qualification_qualifelec_allowed, 'date' => $date]], 200);
                } else {
                    /*
                     *Le lien n'est plus actif
                     * */
                    /* if ((isset($signup->candidate->userInfo->is_final_save) && $signup->candidate->userInfo->is_final_save < 3)) {
                         $error = 'Le lien n\'est plus actif';
                         $error = 'The link  is not active anymore';
                     } else
                         $error = 'The link you are using is invalid please use valid link.';*/

                    return response()->json(['status' => FALSE, 'msg' => __('message.link_not_valid'), 'data' => @$signup->candidate, 'workshop' => $wSetting, 'qualification_qualifelec_allowed' => $qualification_qualifelec_allowed], 200);
                }
            } catch (\Exception $e) {
                $error = 'Something wrong happens.';
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * @param Request $request
         * @return mixed
         */
        public function saveRefrerrerData(Request $request)
        {
            try {
                $response = $this->getReferrerResponse($request->id);
                if (!empty($response)) {
                    $referrer = Referrer::where('id', $request->id)->update(['fname' => $request->fname, 'lname' => $request->lname, 'company' => $request->company, 'referrer_type' => $request->referrer_type, 'position' => $request->position, 'phone' => $request->phone, 'address' => $request->address, 'zip_code' => $request->zip_code]);
                    $pdfData = $this->referrerResponsePdf($response['basic']);
                    $domain = strtok($_SERVER['SERVER_NAME'], '.');
                    if (isset($pdfData[1]))
                        $WorkshopName = $this->core->Unaccent(str_replace(' ', '-', $pdfData[1]->workshop_name));
                    else
                        $WorkshopName = 'Blank';
                    //saving file to s3
                    $fileName = $this->core->localToS3Upload($domain, $WorkshopName, 'REFERRER', $pdfData[0], 'public');
                    if ($fileName) {
                        //this will delete the file from system after uploading to S3.
                        unlink(public_path('public/pdf/' . $pdfData[0]));

                        $referrerData = ReferrerField::where('refreer_id', $request->id)->update(['file' => $fileName, 'uploaded_on' => Carbon::now()]);
                        $this->sendMailsToRefCand($response['basic']);
                        return response()->json(['status' => TRUE, 'msg' => 'Updated', 'data' => @$response['basic']->candidate], 200);
                    } else {
                        $error = 'The link you are using is invalid please use valid link.';
                        return response()->json(['status' => FALSE, 'msg' => $error], 200);

                    }

                } else {
                    return response()->json(['status' => FALSE, 'msg' => 'No Data To Update'], 200);
                }
            } catch (\Exception $e) {

                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * @param $id
         * @return array
         * this function is used to getReferrer saved responce with basic data and his filled custom fields
         */
        public function getReferrerResponse($id, $type = 0)
        {
            try {
                $referrerData = ReferrerField::with('referrer', 'candidate.workshop', 'step')/*->whereNull('uploaded_on')*/
                ->where('refreer_id', $id)->first();
                //&& $type
                if (isset($referrerData->referrer) && !empty($referrerData->referrer)) {
                    if (empty($referrerData->uploaded_on) || $type == 1) {
                        $fields = Skill::with(['userSkill' => function ($b) use ($referrerData) {
                            $b->where(['field_id' => $referrerData->referrer->id, 'type' => 'referrer']);
                        }, 'skillImages', 'skillSelect', 'skillCheckBox', 'skillMeta', 'skillCheckBoxAcceptance'])->whereHas('skillTab', function ($a) {
                            $a->where('tab_type', 6)->whereRaw('JSON_CONTAINS(visible, \'{"referrer": 1}\')');
//                            $a->where('tab_type', 6)->where('visible->referrer',1);
                        })->where('is_valid', 1)->orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->get();
                        return ['basic' => $referrerData, 'custom' => $fields];
                    } else {
                        return [];
                    }

                } else {
                    return [];
                }
            } catch (\Exception $e) {
                return [];
            }
        }

        /**
         * @param $response
         * @return string
         */
        public function referrerResponsePdf($response, $type = 0, $stepId = 0)
        {

            if (isset($response->candidate->workshop) && !empty($response->candidate->workshop)) {
                $workshop_data = Workshop::with('meta')->withoutGlobalScopes()->find($response->candidate->workshop->workshop_id);
                if ((isset($workshop_data->setting['custom_graphics_enable']) && $workshop_data->setting['custom_graphics_enable'] == 1) || isset($workshop_data->setting['pdf'])) {
                    $pdfSeting = $workshop_data->setting['pdf'];
                    $settings_data = json_decode(json_encode($pdfSeting));
                } else {
                    $settings_data = getSettingData('pdf_graphic');
                }
                $file_date = strtotime(Carbon::now());
                $quotation = $this->core->Unaccent(@$response->referrer->fname) . "-" . $this->core->Unaccent(@$response->referrer->lname);

            } else {
                if (isset($response['candidate'])) {
                    $workshop_data = Workshop::with('meta')->withoutGlobalScopes()->find($response['candidate']->workshop->workshop_id);
                    if ((isset($workshop_data->setting['custom_graphics_enable']) && $workshop_data->setting['custom_graphics_enable'] == 1) || isset($workshop_data->setting['pdf'])) {
                        $pdfSeting = $workshop_data->setting['pdf'];
                        $settings_data = json_decode(json_encode($pdfSeting));
                    } else {
                        $settings_data = getSettingData('pdf_graphic');
                    }

                    $file_date = strtotime(Carbon::now());
                    $quotation = $this->core->Unaccent($response['candidate']->fname) . "-" . $this->core->Unaccent($response['candidate']->lname);

                } else {
                    $workshop_data = [];
                    $settings_data = getSettingData('pdf_graphic');
                }

            }

            $pdf_name = str_replace(' ', '-', $quotation) . '-' . $file_date . '.pdf';
            $domain = strtok($_SERVER['SERVER_NAME'], '.');
            //this code must be there means below appending version
            $pdf_name = str_replace(' ', '-', $pdf_name);
            $pdfUrl = public_path('public' . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . $pdf_name);

            if ($type)
                $url = url('/') . "/single-gen-referrer-pdf/" . (isset($response['candidate']) ? $response['candidate']->id : 0) . "/" . (isset($response['candidate']) ? $response['candidate']->id : $response->candidate->id) . "/" . $stepId;
            else
                $url = url('/') . "/gen-referrer-pdf/" . $response->referrer->id . "/" . $response->id;
//        $footer_url = url('/') . "/prepd-footer/" . $response->referrer->id . "/" . $data['wid'];
            //   dd($url);
            $command = 'xvfb-run /home/wkhtmltopdf  --load-error-handling ignore --page-size A4 --margin-bottom 0 --margin-top 0 --margin-right 0 --margin-left 0 --encoding "UTF-8"' . " $url $pdfUrl 2>&1 ";
            // $command = 'xvfb-run /home/wkhtmltopdf --load-error-handling ignore  --orientation portrait --page-size A4 --encoding "UTF-8" --margin-bottom 0 --footer-spacing 10' . " $url $pdfUrl 2>&1 ";
            (shell_exec($command));

            return [$pdf_name, $workshop_data];

        }

        public function getStepReferrerList(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'step_id'      => 'required|',
                    'candidate_id' => 'required|',
                    'card_count'   => 'required|',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                $forIns = 1;
                $fourRenewal = [5, 9, 13, 17, 21, 25];
                $renewal = range(1, 25);
                if ((($request->card_count) % 4 == 0) && (($request->card_count > 0))) {
                    $key = array_search(($request->card_count + 1), $fourRenewal);
                    $forIns = $fourRenewal[$key];
                } else {
                    if ($request->isFinalValue == 0) {
                        $request->card_count = $request->card_count + 1;
                    }
                    $key = array_search(($request->card_count), $renewal);
                    $forIns = 1;
                    foreach ($fourRenewal as $k => $val) {

                        if ($renewal[$key] <= $val) {
                            if ($renewal[$key] >= 5) {
                                if ($k != 0)
                                    $forIns = $fourRenewal[$k - 1];
                                else
                                    $forIns = $fourRenewal[$k];
                                break;
                            } else {
                                $forIns = 1;
                                break;
                            }
                        }
                    }
                }

                $referrerField = ReferrerField::where(['candidate_id' => $request->candidate_id, 'step_id' => $request->step_id, 'for_card_instance' => ($forIns)])->with('referrer:id,fname,lname,email,company,position,referrer_type,pdf_upload,phone,address', 'candidate.userSkillCompany')->get(['id', 'candidate_id', 'field_id', 'refreer_id', 'status', 'for_card_instance', 'step_id', 'used', 'file', 'uploaded_on',
                ]);
                return response()->json(['status' => TRUE, 'data' => $referrerField], 200);

            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        public function useReferrerFile(Request $request)
        {
            try {

                $validator = Validator::make($request->all(), [
                    'ref_field_id' => 'required',
                    'candidate_id' => 'required',
                    //'field_id' => 'required',
                    'type'         => 'required',
                    'skill_id'     => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }

                $referrerField = ReferrerField::where(['id' => $request->ref_field_id])->first(['id', 'file', 'uploaded_on', 'used', 'field_id']);

                $skill = UserSkill::create(['field_id' => $request->candidate_id, 'type' => $request->type, 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'file_input' => $referrerField->file, 'for_card_instance' => (isset($request->cardCount) ? ($request->cardCount + 1) : 0), 'setting' => json_encode(['ref_field_id' => $referrerField->id])]);
                $referrerField->used = 1;
                //$referrerField->field_id = $request->skill_id;
                $referrerField->save();

                return response()->json(['status' => TRUE, 'data' => $skill], 200);

            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        public function removeReferrerFile(Request $request)
        {
            try {

                $validator = Validator::make($request->all(), [
                    'ref_field_id' => 'sometimes',
                    'id'           => 'required|exists:tenant.user_skills,id',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                $skill = UserSkill::where(['id' => $request->id])->orderBy('id', 'desc')->first();
                $ref = json_decode($skill->setting);
                if (isset($ref->ref_field_id) && !empty($ref->ref_field_id)) {
                    $referrerField = ReferrerField::where(['id' => $ref->ref_field_id])->first(['id', 'file', 'uploaded_on', 'used']);
                    $referrerField->used = 0;
                    //$referrerField->field_id = null;
                    $referrerField->save();
                }
                $skill->delete();
                return response()->json(['status' => TRUE, 'data' => @$referrerField], 200);

            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        public function getReferrerFromCandidate($id)
        {
            try {
                $validator = Validator::make(['id' => $id], [
                    'id' => 'required|exists:tenant.referrer_fields,refreer_id',
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }

                $referrerField = $this->getReferrerResponse($id, 1);
                $meta = WorkshopMeta::where('user_id', $referrerField['basic']->candidate_id)->first(['workshop_id']);
                $workshop = Workshop::withoutGlobalScopes()->find($meta->workshop_id);
                $lang = getUserLang($referrerField['basic']->candidate_id);
                $domain = Step::where('is_final_step', 1)->with('domainCheckbox.userSkill')->whereHas('domainCheckbox.userSkill', function ($a) use ($referrerField) {
                    $a->where(['field_id' => $referrerField['basic']->candidate_id, 'type' => 'candidate']);
                })->get();

                $referrerField['candidate'] = $referrerField['basic']->candidate;
                $referrerField['domain'] = isset($referrerField['basic']->step->name) ? $referrerField['basic']->step->name : '';
                $referrerField['workshop'] = @$workshop->setting;
                $referrerField['lang'] = $lang;
                $referrerField['selected_domain'] = $domain;

                return response()->json(['status' => TRUE, 'msg' => $referrerField], 200);

            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }

        }

        public function getReferrerFile($id)
        {
            try {
                $validator = Validator::make(['ref_field_id' => $id], [
                    'ref_field_id' => 'required|exists:tenant.referrer_fields,id',
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                $referrerField = ReferrerField::where(['id' => $id])->first(['id', 'file', 'uploaded_on', 'used']);

                $file = $this->core->getPrivateAsset($referrerField->file, 60);
                return response()->json(['status' => TRUE, 'data' => $file], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }


        public function genBlankAttest($canId, $stepId)
        {
            try {
                $validator = Validator::make(['candidate_id' => $canId, 'step_id' => $stepId], [
                    'candidate_id' => 'required',
                    'step_id'      => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                $referrerData = ReferrerField::with('referrer', 'candidate.workshop')/*->whereNull('uploaded_on')*/
                ->where(['candidate_id' => $canId, 'step_id' => $stepId])->first();
                if (empty($referrerData)) {
                    $referrerData['candidate'] = User::with('workshop')->find($canId);
                    collect($referrerData);
                }
                $response = $this->referrerResponsePdf($referrerData, 1, $stepId);
                $pdfUrl = public_path() . 'public' . '/' . 'pdf' . '/' . $response[0];
                $headers = [
                    'Content-Type' => 'application/pdf',
                ];
                return response()->download($pdfUrl, $response[0], $headers);
                return response()->json(['status' => TRUE, 'data' => $file], 200);
            } catch (\Exception $e) {

                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        public function sendMailsToRefCand($referrerFieldData)
        {
            $lang = session()->has('lang') ? session()->get('lang') : "FR";
            $mailData['mail']['email'] = $referrerFieldData->referrer->email;
            $mailData['url'] = 'http://www.cartetppro.fr/';
            $mailData['mail']['type'] = 'referrer_magic_link_submit_' . $lang;
            $mailData['mail']['template_setting'] = 'referrer_magic_link_submit_' . $lang;
            $this->sendMails($referrerFieldData->referrer->id, ('referrer_magic_link_submit_' . $lang), $mailData, $referrerFieldData->candidate->id);
            // $this->core->SendEmail($mailData, 'dynamic_workshop_template');
            //============================Candidate
            $mailData['mail']['email'] = $referrerFieldData->candidate->email;
            $mailData['mail']['type'] = 'magic_link_received_' . $lang;
            $mailData['mail']['template_setting'] = 'magic_link_received_' . $lang;
            $redirectUrl = url('/#/qualification/registration-form');
            $mailData['url'] = $redirectUrl;
            //updating candidate current step id
            UserMeta::where('user_id', $referrerFieldData->candidate->id)->update(['current_step_id' => isset($referrerFieldData->step->id) ? $referrerFieldData->step->id : getFirstStepId()]);
            return $this->sendMails($referrerFieldData->referrer->id, ('magic_link_received_' . $lang), $mailData, $referrerFieldData->candidate->id);
        }
    }
