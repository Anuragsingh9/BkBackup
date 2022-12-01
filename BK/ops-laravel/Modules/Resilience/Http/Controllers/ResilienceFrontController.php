<?php

    namespace Modules\Resilience\Http\Controllers;
    /**
     * Controller defined to handle request related to consultation reinvent page
     */

    use App\Meeting;
    use App\Setting;
    use Illuminate\Support\Facades\App;
    use App\Entity;
    use App\EntityUser;
    use App\Http\Controllers\CoreController;
    use App\Signup;
    use App\User;
    use App\Workshop;
    use DB;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Hash;
    use Modules\Resilience\Http\Requests\UpdateConsultationAnswerRequest;
    use Modules\Resilience\Services\ResilienceService;
    use Youtube;
    use Validator;
    use Carbon\Carbon;
    use Illuminate\Http\Response;
    use Illuminate\Routing\Controller;
    use Modules\Resilience\Events\InviteFriends;
    use Modules\Resilience\Entities\Consultation;
    use Modules\Resilience\Entities\ConsultationStep;
    use Modules\Resilience\Entities\ConsultationSprint;
    use Modules\Resilience\Entities\ConsultationQuestion;
    use Modules\Resilience\Transformers\SprintResource;
    use Modules\Resilience\Entities\ConsultationAnswer;
    use Modules\Resilience\Http\Requests\QuestionMailRequest;
    use Modules\Resilience\Transformers\ConsultationTransformer;
    use Modules\Resilience\Http\Requests\ConsultationAnswerRequest;
    use Modules\Resilience\Transformers\ConsultationAnswerTransformer;
    use Modules\Resilience\Transformers\ConsultationQuestionTransformer;

    class ResilienceFrontController extends Controller
    {
        private $core;

        public function __construct()
        {
            $this->core = app(CoreController::class);
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);
            $this->workshop = app(\App\Http\Controllers\WorkshopController::class);
            $this->service = ResilienceService::getInstance();
        }

        /**
         * @param User $user
         * @return void
         */
        public function createUserToken(User $user)
        {
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            $token->expires_at = Carbon::now()->addMinutes(1);
            $token->save();
            Auth::guard('api')->user();
            dd($tokenResult->accessToken);
        }


        /**
         * @param QuestionMailRequest $questionMailRequest
         * @return \Illuminate\Http\JsonResponse
         */
        public function sendMail(QuestionMailRequest $questionMailRequest)
        {

            foreach ($questionMailRequest->friend_data as $mail) {
                $data = ($this->prepareEmailData([], 'email_for_invite_friends'));
                event(new InviteFriends('email_template.dynamic_workshop_template', $data, $mail['email']));
            }
            return response()->json(['status' => TRUE, 'data' => TRUE], 200);
        }

        /**
         * Store a newly created resource in storage.
         * @param $consultationId
         * @return Response
         */
        public function show($consultationId = NULL)
        {
            if (empty($consultationId)) {
                $consultation = $this->service->getActive();
            } else {
                $consultation = Consultation::find($consultationId);
            }
            $workshopId = isset($consultation->workshop_id) ? $consultation->workshop_id : NULL;
            $isUserMember = ResilienceService::getInstance()->isUserBelongsToWorkshop($workshopId, NULL, [0], request()->user());
            $isUserAdmin = ResilienceService::getInstance()->isUserBelongsToWorkshop($workshopId);
            if ($isUserAdmin || in_array(Auth::user()->role, ['M1', 'M0'])) {
                $notAllowedUser = FALSE;
            } elseif ($isUserMember) {
                $notAllowedUser = FALSE;
            } else {
                $notAllowedUser = TRUE;
            }
            if ((!isset($consultation->uuid)) || ((!config('constants.Reinvent')) && (isset($consultation->is_reinvent) && $consultation->is_reinvent == 1)) || ($notAllowedUser)) {
                return response()->json(['status' => TRUE, 'msg' => 'No Data Found', 'data' => [], 'is_redirect' => 'https://www.re-invent.solutions/'], 200);
            }
            //here we are checking if consultation having any past meeting
            $consMeetingIds = $consultation->stepMeetings->pluck('meeting_id');
            $meetings = Meeting::whereIn('id', $consMeetingIds)->where(DB::raw("CONCAT(date,' ',start_time)"), '<=', Carbon::now()->format('Y-m-d H:i:00'))->get()->pluck('id');
            //looping the meeting to check and remove step or meeting
            collect($meetings)->each(function ($meet, $key) {
                $this->service->removeMeeting($meet);
            });

            $settingData = Setting::where('setting_key', 'languages_to_show')->first();
            if ($settingData) {
                $enabledLanguages = json_decode($settingData->setting_value);
            } else {
                $enabledLanguages = [];
            }

            if (isset(request()->user()->setting) && !empty(request()->user()->setting)) {
                $lang = json_decode(Auth::user()->setting);
                $lang = isset($lang->lang) ? $lang->lang : 'FR';
            } else {
                $lang = session()->has('lang') ? session()->get('lang') : "FR";
            }

            $grdp = DB::connection('mysql')->table('grdps')->where('type', 0)->first();
            return response()->json(['status' => TRUE, 'data' => (new ConsultationTransformer($consultation))->additional(['showReinvent' => TRUE, 'showStep' => TRUE, 'dateFormat' => TRUE]), 'user' => ['fname' => request()->user()->fname, 'lname' => request()->user()->lname, 'email' => request()->user()->email, 'logo' => request()->user()->avatar, 'other' => request()->user()->role, 'isWadmin' => $isUserAdmin, 'lang' => $lang, 'enabled_languages' => $enabledLanguages, 'grdp' => $grdp]], 200);
        }

        /**
         * Store a newly created resource in storage.
         * @param ConsultationAnswerRequest $consultationAnswerRequest
         * @return void
         */
        public function answer(ConsultationAnswerRequest $consultationAnswerRequest)
        {
            if (!$consultationAnswerRequest->user()) {
                return response()->json(['status' => FALSE, 'msg' => 'Token mismatch or missing'], 500);
            }
            if ($consultationAnswerRequest->has('column_data') && !is_null($consultationAnswerRequest->get('column_data'))) {
                $answer = json_encode($consultationAnswerRequest->get('column_data'));
            } else {
                $answer = $consultationAnswerRequest->get('answer');
            }
            try {
                DB::connection('tenant')->beginTransaction();
                $answer = ConsultationAnswer::create([
                    "consultation_uuid"        => $consultationAnswerRequest->get('consultation_uuid'),
                    "user_id"                  => $consultationAnswerRequest->user()->id,
                    "user_workshop_id"         => $consultationAnswerRequest->get('user_workshop_id'),
                    "consultation_question_id" => $consultationAnswerRequest->get('consultation_question_id'),
                    "answer"                   => $answer,
                    "manual_answer"            => $consultationAnswerRequest->get('is_manual') ? $consultationAnswerRequest->get('manual_answer') : NULL,
                ]);
                $service = ResilienceService::getInstance();
                $service->saveMetaData($consultationAnswerRequest);
                $service->sendNewOptionMail($consultationAnswerRequest);

                DB::connection('tenant')->commit();
                return response()->json(['status' => TRUE, 'data' => new ConsultationAnswerTransformer($answer)], 200);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * Store a newly created resource in storage.
         * @param UpdateConsultationAnswerRequest $updateConsultationAnswerRequest
         * @return void
         */
        public function updateAnswer(UpdateConsultationAnswerRequest $updateConsultationAnswerRequest)
        {
            if (!$updateConsultationAnswerRequest->user()) {
                return response()->json(['status' => FALSE, 'msg' => 'Token mismatch or missing'], 500);
            }
            $answer = ConsultationAnswer::find($updateConsultationAnswerRequest->get('consultation_answer_id'));

            $validator = Validator::make(['user_id' => $updateConsultationAnswerRequest->user()->id], [
                'user_id' => 'required|exists:tenant.consultation_answers,user_id|in:' . $answer->user_id,
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }

            if ($updateConsultationAnswerRequest->has('column_data') && !is_null($updateConsultationAnswerRequest->get('column_data'))) {
                $answerData = json_encode($updateConsultationAnswerRequest->get('column_data'));
            } else {
                $answerData = $updateConsultationAnswerRequest->get('answer');
            }

            try {
                DB::connection('tenant')->beginTransaction();
                $answer->answer = $answerData;
                if ($updateConsultationAnswerRequest->get('is_manual')) {
                    $answer->manual_answer = $updateConsultationAnswerRequest->has('manual_answer') ? $updateConsultationAnswerRequest->get('manual_answer') : NULL;
                } else {
                    $answer->manual_answer = NULL;
                }
                $answer->save();
                $service = ResilienceService::getInstance();
                $service->saveMetaData($updateConsultationAnswerRequest);
                $service->sendNewOptionMail($updateConsultationAnswerRequest);
                DB::connection('tenant')->commit();
                return response()->json(['status' => TRUE, 'data' => new ConsultationAnswerTransformer($answer)], 200);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }


        /**
         * to get QuestionType Step Questions Data
         * @param $id
         * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
         */
        public function getQuestionById($id)
        {
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|exists:tenant.consultation_steps,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            $step = ConsultationStep::has('consultationQuestion')->where(['id' => $id, 'step_type' => 2])->first();

            if (!$step) {
                return response()->json(['status' => FALSE, 'msg' => 'Data not found'], 422);
            }
            $sprint = ConsultationSprint::with('consultation:uuid')->find($step->consultation_sprint_id);
            $data = ['date' => Carbon::today(), 'sprint_id' => $sprint->id, 'step_id' => $step->id];

            return response()->json(['status' => TRUE, 'data' => (ConsultationQuestionTransformer::collection(isset($step->consultationQuestion) ? $step->consultationQuestion : collect([])))], 200);
        }

        /**
         * Show the form for editing the specified resource.
         * @param $id
         * @return Response
         */
        public function stepsBySprintId($sprintId)
        {
            $validator = Validator::make(['id' => $sprintId], [
                'id' => 'required|exists:tenant.consultation_sprints,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }

            $sprint = ConsultationSprint::with('consultation:uuid')->find($sprintId);
            $data = ['date' => Carbon::today(), 'sprint_id' => $sprint->id];
            return response()->json(['status' => TRUE, 'data' => (new SprintResource($sprint))->additional(['showStepData' => TRUE])], 200);
        }

        public function uploadFileGetUrl($image, $folder = '', $visibility = 'public')
        {
            $imageUrl = '';
            if ($image) {
                $hostname = $this->tenancy->hostname()['fqdn'];
                $domain = strtok($hostname, '.');
                $filePath = $domain . '/consultation/' . $folder;
                $imageUrl = ($this->core->fileUploadToS3($filePath, ($image), $visibility));
            }
            return $imageUrl;
        }


        public function register(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'email'      => 'required|unique:tenant.users,email',
                    'fname'      => 'required|regex:/^[0-9a-zA-Zu00E0-u00FC&àâäèéêëîïôœùûüÿççÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ _\'\ "-]*$/m',
                    'lname'      => 'required|regex:/^[0-9a-zA-Zu00E0-u00FC&àâäèéêëîïôœùûüÿççÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ _\'\ "-]*$/m',
                    'union_id'   => 'sometimes|required',
                    'union_name' => 'sometimes|required',
                    'class_uuid' => 'sometimes|required|exists:tenant.consultation_signup_classes,uuid',
                    'position'   => 'required',
                    //'consultation_uuid' => 'required|exists:tenant.consultations,uuid',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                return $this->service->addUser($request);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'data' => 'Internal server error in adding User ' . $e->getMessage()], 500);
            }

        }

        /*
    To register external the member from wordpress to our event
    */


        public function genReinventLink($uuid)
        {
            $consultation = Consultation::where('uuid', $uuid)->first(['uuid']);
            if (isset($consultation->uuid)) {
                $hostname = $this->tenancy->hostname()['fqdn'];
                $domain = strtok($hostname, '.');
                if (!empty(env('REINVENT_URL'))) {
                    $params = env('HOST_TYPE') . $domain . '.' . env('REINVENT_URL');
                } else {
                    $params = env('HOST_TYPE') . $domain . '.' . 're-invent.solutions/#/';
                }
                $user = Auth::user();
                $token_result = $user->createToken('Personal Access Token');
                $token = $token_result->token;
                $token->expires_at = now()->addHours(12);
                $token->save();

                $params = $params . $token_result->accessToken;
            }

            return redirect()->away($params);
        }

        public function checkCode(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'id'    => 'required',
                'email' => 'required',
                'code'  => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            $data = $this->service->checkCode($request);
            return response()->json($data);
        }

        public function forgotPassword(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:tenant.users,email',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }

            if (session()->has('lang')) {
                App::setLocale(strtolower(session('lang')));
            }
            $user = User::where('email', $request->email)->first();

            if (isset($user->id)) {
                $key = generateRandomString(36);
                $user->identifier = $key;
                $user->save();
                $rootLink = $request->has('link') ? 'https://' . $request->get('link') : $request->root();
                $link = $rootLink . '/#/reinvent-set-password/' . $request->email . '/' . $key;
                $mailData['mail'] = ['subject' => "Réinitialisation de votre mot de passe", 'email' => $request->email, 'firstname' => 'OP Simplify', 'url' => $link, 'user' => $user];
                if ($this->core->SendEmail($mailData, 'forget-password')) {
                    return response()->json(['status' => TRUE, 'success' => __('message.FLASH_RESET_PASS_LINK_SEND')]);
                } else {
                    return response()->json(['status' => FALSE, 'msg' => __('message.FLASH_RESET_PASS_LINK_SEND_FAIL')]);
                }
            } else {
                return response()->json(['status' => FALSE, 'msg' => __('message.FLASH_INVALID_EMAIL_ADDRESS')]);
            }
        }

        public function setPassword(Request $request)
        {
            if (session()->has('lang')) {
                App::setLocale(strtolower(session('lang')));
            }
            if ($request->identifier == '' || $request->new_password == '' || $request->confirm_password == '') {
                return response()->json(['status' => FALSE, 'msg' => __('message.FLASH_ALL_FIELD_REQUIRED')]);
            }
            if (strlen($request->new_password) < 6) {
                return response()->json(['status' => FALSE, 'msg' => __('message.FLASH_CPASS_LENGTH')]);
            }
            if ($request->new_password != $request->confirm_password) {
                return response()->json(['status' => FALSE, 'msg' => __('message.FLASH_NPASS_CPASS_NOT_MATCH')]);
            }
            if (User::where('identifier', $request->identifier)->count() == 1) {
                User::where('identifier', $request->identifier)->update(['password' => Hash::make($request->new_password), 'identifier' => NULL]);
                return response()->json(['status' => TRUE, 'msg' => __('message.FLASH_RESET_PASS_SUCCESS')]);
            } else {
                return response()->json(['status' => FALSE, 'msg' => __('message.FLASH_RESET_PASS_FAIL')]);
            }
        }

        public function changePassword(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'password'        => 'required',
                'confirmPassword' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }

            if ($request->password == '' || $request->confirmPassword == '')
                return response()->json(['status' => FALSE, 'msg' => config('constants.FLASH_ALL_FIELD_REQUIRED')]);
            if (strlen($request->password) < 6)
                return response()->json(['status' => FALSE, 'msg' => config('constants.FLASH_CPASS_LENGTH')]);
            if ($request->password != $request->confirmPassword)
                return response()->json(['status' => FALSE, 'msg' => config('constants.FLASH_NPASS_CPASS_NOT_MATCH')]);

            $result = User::where('id', $request->user()->id)->update(['password' => Hash::make($request->password)]);
            if ($result) {
                User::where('id', $request->user()->id)->increment('login_count');
                return response()->json(['status' => TRUE, 'msg' => '']);
            } else {
                return response()->json(['status' => FALSE, 'msg' => config('constants.FLASH_RESET_PASS_FAIL')]);
            }
        }

        /*
         * this function we used to change system language based on token
         * */
        public function languageChange(Request $request)
        {
            session()->put('lang', $request->lang);
            if (isset($request->user()->id)) {
                if (isset($request->lang) && !empty($request->lang)) {
                    $user = User::where('id', $request->user()->id)->update(['setting' => '{"lang":"' . $request->lang . '"}']);
                    $lang = json_decode(User::find($request->user()->id)->setting);
                    //return response($lang->lang);
                } else {
                    $lang = json_decode($request->user()->setting);
                    //return response($lang->lang);
                }
            }
            return response(session()->get('lang'));
        }

        public function apiLogin(Request $request)
        {
            try {
                $credentials = ['email' => $request->email, 'password' => $request->password];
                if (Auth::validate($credentials)) {
                    $user = User::with(['entity' => function ($a) {
                        $a->where('entity_type_id', 3);
                    }, 'entity.entityLabel'])->where('email', $request->email)->first();

                    if (Hash::check($request->password, $user->password)) {
                        /* if ($user->on_off == 0) {
                             return response()->json(['status' => FALSE, 'msg' => 'Verify first !'], 422);
                         }*/
                        if (!empty($user->entity)) {
                            $position = 0;
                            $_union = collect($user->entity)->where('entity_type_id', 3)->first();
                            if (isset($_union->id)) {
                                $position = collect($_union->pivot)->where('entity_label', '!=', NULL)->count();
                            }
                        } else
                            $position = 0;

                        $request->merge(['firstname' => stripcslashes($user->fname), 'lastname' => stripcslashes($user->lname)]);
                        $this->service->addMember($request);
                        return response()->json([
                            'status' => TRUE,
                            'data'   => [
                                'position'     => $position,
                                'unions'       => $user->entity,
                                'fname'        => $user->fname,
                                'lname'        => $user->lname,
                                'email'        => $user->email,
                                'access_token' => $user->createToken('check')->accessToken,
                            ],
                        ]);
                    } else // if we have fault and mistakenly 2 user exists with same email
                        return response()->json(['status' => FALSE, 'msg' => 'Contact Support Team for this issue'], 422);
                }
                return response()->json(['status' => FALSE, 'msg' => __('auth.failed')], 422);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
            }
        }

        public function updateUnionPosition(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'union_id'   => 'sometimes|required',
                'position'   => 'required',
                'union_name' => 'sometimes|required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }

            if (!isset($request->union_id)) {
                $union = Entity::updateOrCreate([
                    'long_name'      => $request->union_name,
                    'short_name'     => $request->union_name,
                    'entity_type_id' => 3,
                    //'created_by'     => Auth::user()->id,
                ], [
                    'long_name'      => $request->union_name,
                    'entity_type_id' => 3,
                    //'created_by'     => Auth::user()->id,
                ]);
            }
            $eventUser = EntityUser::updateOrCreate(
                ['user_id'   => $request->user()->id,
                 'entity_id' => (!isset($request->union_id) ? $union->id : $request->union_id)],
                [
                    'user_id'      => $request->user()->id,
                    'entity_id'    => (!isset($request->union_id) ? $union->id : $request->union_id),
                    'entity_label' => isset($request->union_position) ? $request->union_position : $request->position,
                ]);
            return response()->json(['status' => TRUE, 'msg' => __('message.SUCCESS_UPDATE')], 200);
        }

        public function checkCount(Request $request)
        {
            $service = ResilienceService::getInstance();
            dd($service->testConsultationIsAnswered($request));
        }

        /*
         * this function is testing function for query of meeting del
         * */
        public function sqlToCheckMeetingDel($consultation)
        {
            if (empty($consultationId)) {
                $consultation = $this->service->getActive();
            } else {
                $consultation = Consultation::find($consultation);
            }

            //here we are checking if consultation having any past meeting
            $consMeetingIds = $consultation->stepMeetings->pluck('meeting_id');
            $meetings = Meeting::whereIn('id', $consMeetingIds)->where(DB::raw("CONCAT(date,' ',start_time)"), '<=', Carbon::now()->format('Y-m-d H:i:00'))->get()->pluck('id');
            //looping the meeting to check and remove step or meeting
            collect($meetings)->each(function ($meet, $key) {
                dump($meet);
//                $this->service->removeMeeting($meet);
            });

            return response()->json(['status' => TRUE, 'data' => $meetings, 'mid' => $consMeetingIds], 200);
            dd($meetings->toSql(), $consMeetingIds);
        }
    }
