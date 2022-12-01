<?php

    namespace Modules\Resilience\Http\Controllers;

    use App\Http\Controllers\CoreController;
    use App\Setting;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Routing\Controller;
    use Illuminate\Support\Facades\App;
    use Illuminate\Support\Facades\Auth;
    use Modules\Resilience\Entities\Consultation;
    use Modules\Resilience\Entities\ConsultationQuestionType;
    use Modules\Resilience\Entities\ConsultationStep;
    use Modules\Resilience\Entities\ConsultationAnswer;
    use Modules\Resilience\Events\InviteFriends;
    use Modules\Resilience\Entities\ConsultationQuestion;
    use Modules\Resilience\Http\Requests\AddQuestionRequest;
    use Modules\Resilience\Http\Requests\QuestionFriendsMailRequest;
    use Modules\Resilience\Http\Requests\QuestionMailRequest;
    use Modules\Resilience\Http\Requests\UpdateConsultationQuestionRequest;
    use Modules\Resilience\Listeners\EmailToFriends;
    use Modules\Resilience\Transformers\ConsultationQuestionTransformer;
    use Modules\Resilience\Transformers\ConsultationQuestionTypeTransformer;
    use Modules\Resilience\Transformers\ConsultationStepAssetTransformer;
    use DB;
    use Validator;
    use Modules\Resilience\Transformers\ConsultationStepTransformer;

    /**
     * Class QuestionController
     * @package Modules\Resilience\Http\Controllers
     */
    class QuestionController extends Controller
    {
        /**
         * QuestionController constructor.
         */
        private $core;

        public function __construct()
        {
            $this->core = app(CoreController::class);
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);
        }

        /**
         * Display a listing of the resource.
         * @param $consultationQuestionId
         * @return Response
         */
        public function index($consultationQuestionId)
        {
            $consultationQuestion = ConsultationQuestion::find($consultationQuestionId);
            if (!$consultationQuestion) {
                return response()->json(['status' => FALSE, 'msg' => 'Question Not Found'], 500);
            }
            return response()->json(['status' => TRUE, 'data' => new ConsultationQuestionTransformer($consultationQuestion)], 200);
        }

        /**
         * Show the form for creating a new resource.
         * @return Response
         */
        public function create()
        {
            return view('resilience::create');
        }

        /**
         * Show the specified resource.
         * @return Response
         */
        public function show()
        {
            return view('resilience::show');
        }

        /**
         * Show the form for editing the specified resource.
         * @param $consultationQuestionId
         * @param UpdateConsultationQuestionRequest $updateConsultationQuestionRequest
         * @return Response
         */
        public function edit($consultationQuestionId, UpdateConsultationQuestionRequest $updateConsultationQuestionRequest)
        {
            $validator = Validator::make(['id' => $consultationQuestionId], [
                'id' => 'required|exists:tenant.consultation_questions,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            $consultationQuestion = ConsultationQuestion::find($consultationQuestionId);
            try {
                DB::connection('tenant')->beginTransaction();
//                if ($updateConsultationQuestionRequest->has('consultation_question_type_id') && !empty($updateConsultationQuestionRequest->consultation_question_type_id)) {
//                    $consultationQuestion->consultation_question_type_id = (int)$updateConsultationQuestionRequest->consultation_question_type_id;
//                }
                if ($updateConsultationQuestionRequest->has('question') && !empty($updateConsultationQuestionRequest->question)) {
                    $consultationQuestion->question = $updateConsultationQuestionRequest->question;
                }
                if ($updateConsultationQuestionRequest->has('description')) {
                    $consultationQuestion->description = $updateConsultationQuestionRequest->description;
                }
                if ($updateConsultationQuestionRequest->has('comment')) {
                    $consultationQuestion->comment = $updateConsultationQuestionRequest->comment;
                }
                if ($updateConsultationQuestionRequest->has('is_mandatory')) {
                    $consultationQuestion->is_mandatory = $updateConsultationQuestionRequest->is_mandatory;
                }

                if ($updateConsultationQuestionRequest->has('displayFriendRequest')) {
                    $consultationQuestion->displayFriendRequest = $updateConsultationQuestionRequest->displayFriendRequest;
                }
                if ($updateConsultationQuestionRequest->has('allow_add_other_answers')) {
                    $consultationQuestion->allow_add_other_answers = $updateConsultationQuestionRequest->allow_add_other_answers;
                }
                if ($updateConsultationQuestionRequest->has('options')) {
                    $consultationQuestion->options = $this->validateTypeOrGenerateOptionId($updateConsultationQuestionRequest, $consultationQuestion);
                }
                $consultationQuestion->save();
                DB::connection('tenant')->commit();
                return response()->json(['status' => TRUE, 'data' => new ConsultationQuestionTransformer($consultationQuestion)], 200);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * Update the specified resource in storage.
         * @param Request $request
         * @return void
         */
        public function update(Request $request)
        {
            //
        }

        /**
         * Remove the specified resource from storage.
         * @param $consultationQuestionId
         * @return void
         */
        public function destroy($consultationQuestionId)
        {
            $validator = Validator::make(['id' => $consultationQuestionId], [
                'id' => 'required|exists:tenant.consultation_questions,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            try {
                $delete = ConsultationQuestion::whereId($consultationQuestionId)->delete();
                if ($delete)
                    return response()->json(['status' => TRUE, 'data' => 'Question Deleted Successfully'], 200);
                else
                    return response()->json(['status' => FALSE, 'msg' => 'Invalid Id'], 500);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * Store a newly created resource in storage.
         * @param AddQuestionRequest $addQuestionRequest
         * @param $consultationStepId
         * @return Response
         */
        public function store(AddQuestionRequest $addQuestionRequest, $consultationStepId)
        {
            $validator = Validator::make(['consultationStepId' => $consultationStepId], [
                'consultationStepId' => 'required|numeric|exists:tenant.consultation_steps,id|in:' . $addQuestionRequest->get('consultation_step_id'),
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            try {
                DB::connection('tenant')->beginTransaction();
                $consultationStep = ConsultationStep::find($consultationStepId);
                if (!$consultationStep) {
                    $question = ConsultationQuestion::create([
                        "consultation_step_id"          => $consultationStepId,
                        "consultation_question_type_id" => $addQuestionRequest->get('consultation_question_type_id'),
                        "question"                      => $addQuestionRequest->get('question'),
                        "description"                   => $addQuestionRequest->get('description'),
                        "comment"                       => $addQuestionRequest->get('comment'),
                        "is_mandatory"                  => $addQuestionRequest->get('is_mandatory'),
                        "displayFriendRequest"          => $addQuestionRequest->get('displayFriendRequest'),
                        "allow_add_other_answers"       => $addQuestionRequest->get('allow_add_other_answers'),
                        "options"                       => $this->validateTypeOrGenerateOptionId($addQuestionRequest),
                        "order"                         => $this->getQuestionOrder($consultationStep),
                        'sort_order'                    => $this->getSortOrder($consultationStepId),
                    ]);
                } else {
                    $question = $consultationStep->consultationQuestion()->create([
                        "consultation_step_id"          => $consultationStepId,
                        "consultation_question_type_id" => $addQuestionRequest->get('consultation_question_type_id'),
                        "question"                      => $addQuestionRequest->get('question'),
                        "description"                   => $addQuestionRequest->get('description'),
                        "comment"                       => $addQuestionRequest->get('comment'),
                        "is_mandatory"                  => $addQuestionRequest->get('is_mandatory'),
                        "displayFriendRequest"          => $addQuestionRequest->get('displayFriendRequest'),
                        "allow_add_other_answers"       => $addQuestionRequest->get('allow_add_other_answers'),
                        "options"                       => $this->validateTypeOrGenerateOptionId($addQuestionRequest),
                        "order"                         => $this->getQuestionOrder($consultationStep),
                        'sort_order'                    => $this->getSortOrder($consultationStepId),
                    ]);
                }
                DB::connection('tenant')->commit();
                return response()->json(['status' => TRUE, 'data' => new ConsultationQuestionTransformer($question)], 200);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        public function generateColumnId($columnData)
        {
            foreach ($columnData as $key => $value) {
                foreach ($value as $k => $v) {
                    $value[$k]['id'] = $key . '' . $k;
                }
                $columnData[$key] = $value;
            }
            return json_encode($columnData);
        }

        public function validateTypeOrGenerateOptionId($addQuestionRequest, $consultationQuestion = [])
        {
            if ($addQuestionRequest->get('consultation_question_type_id') === "16") {
                return $this->generateColumnId($addQuestionRequest->get('column_data'));
            }
            if (!$addQuestionRequest->has('options') || !$this->isJson($addQuestionRequest->get('options'))) {
                return NULL;
            }

            $options = json_decode($addQuestionRequest->get('options'));
            if (!empty($consultationQuestion)) {
                $qOptions = collect(json_decode($consultationQuestion->options));
                //remove non existing values from saved collection
                $qOptions = $qOptions->reject(function ($value, $key) use ($options) {
                    return !(collect($options)->contains('label', $value->label));
                })->values();
                $newValues = collect([]);
                foreach ($options as $k => $option) {
                    if (!$qOptions->contains('label', $option->label)) {
                        $option->id = rand(100, 999) . "-" . rand(10000, 99999) . "-" . $addQuestionRequest->get('consultation_step_id') . "-" . $addQuestionRequest->get('consultation_question_type_id');
                        $newValues->put($k, $option);
                    } else {
                        $key = $qOptions->pluck('label')->search($option->label);
                        if ($key != FALSE || $key == 0) {
                            $option->id = $qOptions[$key]->id;
                            $newValues->put($k, $option);
                        }
                    }
                }
                return json_encode($newValues->values()->toArray());
            } else {
                foreach ($options as $option) {
                    $option->id = rand(100, 999) . "-" . rand(10000, 99999) . "-" . $addQuestionRequest->get('consultation_step_id') . "-" . $addQuestionRequest->get('consultation_question_type_id');
                }
                return json_encode($options);
            }

        }

        public function getQuestionOrder($consultationStep)
        {
            if (!$consultationStep) {
                return 1;
            }
            $order = $consultationStep->consultationQuestion()->count();
            return $order + 1;
        }

        public function isJson($string)
        {
            return (is_string($string) && (is_object(json_decode($string)) || is_array(json_decode($string)))) ? TRUE : FALSE;
        }

        /**
         * Store a newly created resource in storage.
         * @return void
         */
        public function getAsset()
        {
            //
        }

        /**
         * @param QuestionMailRequest $questionMailRequest
         * @return \Illuminate\Http\JsonResponse
         */
        public function sendMail(QuestionMailRequest $questionMailRequest)
        {

            $consultation = Consultation::where('uuid', $questionMailRequest->consultation_uuid)->first(['uuid', 'workshop_id']);
            if (isset($consultation->workshop->id)) {
                $workshop = $consultation->workshop;
            } else {
                return response()->json(['status' => TRUE, 'data' => 'Something Wrong'], 500);
            }
            $hostname = $this->tenancy->hostname()['fqdn'];
            $domain = strtok($hostname, '.');
            if (!empty(env('REINVENT_URL'))) {
                $params = env('HOST_TYPE') . $domain . '.' . env('REINVENT_URL');
            } else {
                $params = env('HOST_TYPE') . $domain . '.' . 're-invent.solutions?';
            }
            $params = $params . $consultation->uuid;
            foreach ($questionMailRequest->friend_data as $mail) {
                $data = ($this->prepareEmailData($workshop, 'email_for_invite_friends'));
                $data['mail']['url'] = $params;
                event(new InviteFriends('email_template.dynamic_workshop_template', $data, $mail['email']));
            }
            return response()->json(['status' => TRUE, 'data' => TRUE], 200);
        }

        /**
         * @param array $workshopData
         * @param $key
         * @return array
         */
        public function prepareEmailData($workshopData = [], $key)
        {
            $currUserFname = isset(Auth::user()->fname) ? Auth::user()->fname : request()->user()->fname;
            $currUserLname = isset(Auth::user()->lname) ? Auth::user()->lname : request()->user()->lname;

            $setting = $this->getSetting($key);
            //   $member = [];
            $member = workshopValidatorPresident($workshopData);
            $wsetting = getWorkshopSettingData($workshopData->id);
            $getOrgDetail = getOrgDetail();
            $WorkshopSignatory = getWorkshopSignatoryData($workshopData->id);
            $keywords = [
                '[[ParticipantFN]]' => (isset($participant) ? $participant->fname : ''),
                '[[ParticipantLN]]' => (isset($participant) ? $participant->lname : ''),

                '[[WorkshopShortName]]'         => $workshopData->code1,
                '[[WorkshopLongName]]'          => $workshopData->workshop_name,
                '[[WorkshopPresidentFullName]]' => @$member['p']['fname'] . ' ' . $member['p']['lname'],
                '[[WorkshopvalidatorFullName]]' => @ $member['v']['fname'] . ' ' . $member['v']['lname'],
                '[[ValidatorEmail]]'            => @$member['v']['email'],
                '[[PresidentEmail]]'            => @$member['p']['email'],
                '[[OrgName]]'                   => $getOrgDetail->name_org,
                '[[SignatoryFname]]'            => isset($WorkshopSignatory['signatory_fname']) ? $WorkshopSignatory['signatory_fname'] : '',
                '[[Signatorylname]]'            => isset($WorkshopSignatory['signatory_lname']) ? $WorkshopSignatory['signatory_lname'] : '',
                '[[SignatoryPossition]]'        => isset($WorkshopSignatory['signatory_possition']) ? $WorkshopSignatory['signatory_possition'] : '',
                '[[SignatoryEmail]]'            => isset($WorkshopSignatory['signatory_email']) ? $WorkshopSignatory['signatory_email'] : '',
                '[[SignatoryPhone]]'            => isset($WorkshopSignatory['signatory_phone']) ? $WorkshopSignatory['signatory_phone'] : '',
                '[[SignatoryMobile]]'           => isset($WorkshopSignatory['signatory_mobile']) ? $WorkshopSignatory['signatory_mobile'] : '',
            ];
            $subject = str_replace(array_keys($keywords), array_values($keywords), json_decode($setting->setting_value)->email_subject);
            $mailData = $keywords;
            $mailData['mail']['subject'] = $subject;
            // $mailData['mail']['email'] = 'server3382@gmail.com';
            $mailData['mail']['firstname'] = $currUserFname;
            $mailData['mail']['lastname'] = $currUserLname;
            $mailData['mail']['workshop_data'] = $workshopData;
            $mailData['mail']['template_setting'] = $setting->setting_key;
//            $mailData['mail']['participant'] = $participant;
            return ($mailData);
        }

        /**
         * @param $key
         * @param string $lang
         * @return mixed
         */
        public function getSetting($key, $lang = '')
        {
            $lang = (App::getLocale() == 'fr') ? '_FR' : '_EN';
            $key = $key . $lang;
            $data = Setting::where('setting_key', $key)->first();
            return ($data) ? json_decode($data) : $data;
        }

        public function getQuestionTypes()
        {
            $consultationQuestionTypes = ConsultationQuestionType::all();
            return response()->json(['status' => TRUE, 'data' => ConsultationQuestionTypeTransformer::collection($consultationQuestionTypes)], 200);

        }

        /**
         * Store a newly created resource in storage.
         * @param $consultationSprintId
         * @return Response
         */
        public function getPendingSteps($consultationSprintId)
        {
            $ans = consultationQuestion::where('is_mandatory', 1)->whereHas('ConsultationAnswer', function ($q) {
                $q->where('user_id', Auth::id());
            })->whereHas('consultationStep', function ($q) use ($consultationSprintId) {
                $q->where('consultation_sprint_id', $consultationSprintId);
            })->get()->groupBy('consultation_step_id');
            $questionSteps = ConsultationStep::where('consultation_sprint_id', $consultationSprintId)->withoutGlobalScopes()->where('step_type', 2)->whereNotIn('id', $ans->keys())->get(['id', 'title']);
            return response()->json(['data' => $questionSteps, 'status' => TRUE], 200);
        }

        public function sendQuestionMail(QuestionFriendsMailRequest $questionFriendsMailRequest)
        {
            $data['mail']['subject'] = $questionFriendsMailRequest->subject;
            $data['mail']['msg'] = $questionFriendsMailRequest->body;
            $data['mail']['email'] = $questionFriendsMailRequest->mail_to;

            return response()->json(['status' => TRUE, 'data' => ($this->core->SendEmail($data)) ? TRUE : FALSE], 200);
        }

        /**
         * @param int $uuid
         * @return int
         */
        protected function getSortOrder($stepId = 1)
        {
            $sortOrder = consultationQuestion::where('consultation_step_id', $stepId)->orderByRaw('CAST(sort_order AS UNSIGNED) desc')->first(['sort_order']);
            if (isset($sortOrder->sort_order)) {
                return ($sortOrder->sort_order + 1);
            } else {
                return 0;
            }
            //  return (consultationQuestion::where('consultation_step_id', $stepId)->count() + 1);
        }
    }
