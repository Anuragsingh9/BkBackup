<?php

    namespace Modules\Resilience\Http\Controllers;
    /**
     * Controller defined to handle request related to consultation
     */

    use Auth;
    use App\Meeting;
    use App\Workshop;
    use App\WorkshopMeta;
    use Carbon\Carbon;
    use DB;
    use Illuminate\Http\JsonResponse;
    use Laravel\Passport\Client;
    use Laravel\Passport\TokenRepository;
    use Modules\Resilience\Entities\ConsultationStepMeeting;
    use Modules\Resilience\Entities\ConsultationQuestion;
    use Modules\Resilience\Http\Requests\StoreMeetingStep;
    use Modules\Resilience\Http\Requests\UpdateConsultationRequest;
    use Modules\Resilience\Http\Requests\UpdateSprintStepQuestionOrderRequest;
    use Modules\Resilience\Http\Requests\UpdateThankYouRequest;
    use Modules\Resilience\Services\ResilienceService;
    use Modules\Resilience\Transformers\MeetingStepResource;
    use Modules\Resilience\Http\Requests\UpdateStepRequest;
    use Maatwebsite\Excel\Facades\Excel;
    use Modules\Resilience\Exports\AnswerExport;
    use Youtube;
    use Validator;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Routing\Controller;
    use App\Http\Controllers\CoreController;
    use Modules\Resilience\Entities\Consultation;
    use Modules\Resilience\Entities\ConsultationStep;
    use Modules\Resilience\Entities\ConsultationAnswer;
    use Modules\Resilience\Entities\ConsultationSprint;
    use Modules\Resilience\Http\Requests\AddStepRequest;
    use Modules\Resilience\Http\Requests\AddAssetRequest;
    use Modules\Resilience\Http\Requests\AddWelcomeRequest;
    use Modules\Resilience\Http\Requests\AddThankYouRequest;
    use Modules\Resilience\Http\Requests\AddQuestionRequest;
    use Modules\Resilience\Http\Requests\SidebarStepRequest;
    use Modules\Resilience\Http\Requests\UpdateWelcomeRequest;
    use Modules\Resilience\Transformers\ConsultationTransformer;

    use Modules\Resilience\Http\Requests\CreateConsultationRequest;
    use Modules\Resilience\Http\Requests\ConsultationAnswerRequest;
    use Modules\Resilience\Transformers\ConsultationStepTransformer;
    use Modules\Resilience\Transformers\ConsultationAnswerTransformer;
    use Modules\Resilience\Transformers\ConsultationQuestionTransformer;
    use Modules\Resilience\Transformers\ConsultationStepAssetTransformer;


    class ResilienceController extends Controller
    {
        private $core;

        public function __construct()
        {
            $this->core = app(CoreController::class);
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);
        }

        const CAST = 'CAST(sort_order AS UNSIGNED) ASC';

        /**
         * Store a newly created resource in storage.
         * @param SidebarStepRequest $sidebarStepRequest
         * @return void
         */
        public function sidebarStep(SidebarStepRequest $sidebarStepRequest)
        {
            $step = NULL;
            if ($sidebarStepRequest->has('consultation_sprint_id') && $sidebarStepRequest->has('step_type')) {
                $step = ConsultationStep::where('consultation_sprint_id', $sidebarStepRequest->get('consultation_sprint_id'))->where('step_type', $sidebarStepRequest->get('step_type'))->first();
            } elseif ($sidebarStepRequest->has('consultation_step_id')) {
                $step = ConsultationStep::find($sidebarStepRequest->get('consultation_step_id'));
            } else {
                return response()->json(['status' => FALSE, 'msg' => 'No Data Found', 'data' => []], 200);
            }
            if (!$step) {
                return response()->json(['status' => FALSE, 'msg' => 'No Data Found', 'data' => []], 200);
            }
            return response()->json(['data' => (new ConsultationStepTransformer($step))->additional(['showStepData' => TRUE, 'showAnswerCountData' => TRUE]), 'status' => TRUE], 200);

        }

        /**
         * Store a newly created resource in storage.
         * @param $consultationId
         * @return void
         */
        public function sidebar($consultationId)
        {
            $validator = Validator::make(['uuid' => $consultationId], [
                'uuid' => 'required|exists:tenant.consultations,uuid',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            $consultation = Consultation::with('consultationSprint:id,title,consultation_uuid', 'consultationSprint.consultationStep:id,title,consultation_sprint_id,step_type', 'consultationSprint.consultationStep.consultationQuestion:id,question,consultation_step_id,consultation_question_type_id', 'consultationSprint.consultationStep.consultationQuestion.consultationQuestionType:id,question_type')->find($consultationId, ['uuid', 'name',
                'internal_name']);
            return response()->json(['status' => TRUE, 'data' => $consultation], 200);

        }

        /**
         * Store a newly created resource in storage.
         * @param Request $request
         * @param $id
         * @param $tense
         * @param null $itemPerPage
         * @return Response|JsonResponse
         */
        public function index(Request $request, $id, $itemPerPage = 10)
        {
            try {
                $validator = Validator::make(['wid' => $id], [
                    'wid' => 'required|exists:tenant.workshops,id',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }

                $meta = WorkshopMeta::where(['user_id' => Auth::user()->id, 'workshop_id' => $id])->first();
                if ((!isset($meta->id)) && (!in_array(Auth::user()->role, ['M1', 'M0']))) {
                    if (!isset($meta->id)) {
                        return response()->json(['status' => FALSE, 'msg' => 'Member is not authorized'], 422);
                    }
                }

                if ($itemPerPage && (!is_numeric($itemPerPage) || $itemPerPage < 1)) {
                    return response()->json(['status' => FALSE, 'msg' => 'Invalid Page Items'], 422);
                }
                $workshop = Workshop::withoutGlobalScopes()->find($id);
                // SORTING PARAMTERS
                $field = $request->field;
                $order = $request->order;
                $possibleOrder = ['name', 'created_at', 'is_reinvent', 'start_date', 'end_date'];
                $field = (($request->field && in_array($request->field, $possibleOrder)) ? $request->field : 'created_at');
                $order = (($request->order && ($request->order == 'desc' || $request->order == 'asc')) ? $request->order : 'asc');

                $consultations = Consultation::where('workshop_id', $id)->orWhere(function ($a) use ($workshop) {
                    if (!$workshop->is_dependent) {
                        $a->orWhereIn('workshop_id', function ($query) use ($workshop) {

                            $query->select('workshops.id')
                                ->from('workshops')
                                ->where(['workshop_type' => 1, 'is_dependent' => 1, 'code1' => $workshop->code1]);
                        });
                    }
                })->groupBy('consultations.uuid');
                // ORDERING THE RESULT
                if ($field == 'start_date') {
                    $orderedData = $consultations->orderBy($field, $order)
                        ->orderBy('start_date', $order);
                } else {
                    $orderedData = $consultations->orderBy($field, $order)
                        ->orderBy('created_at', $order);
                }
                if ($itemPerPage) {
                    $orderedData = $orderedData->get();
                } else {
                    $orderedData = $orderedData->get();
                }
                if ((isset($meta->role) && $meta->role == 0) && in_array(Auth::user()->role, ['M2'])) {
                    $orderedData = $orderedData->filter(function ($item) {
                        if (($item->start_date <= Carbon::today()->format('Y-m-d')) && ($item->display_results_until >= Carbon::today()->format('Y-m-d'))) {
                            return $item;
                        }
                    })->values();
                }

                return response()->json(['status' => TRUE, 'data' => (ConsultationTransformer::collection($orderedData))->additional(['showStep' => FALSE, 'dateFormat' => TRUE])], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * Store a newly created resource in storage.
         * @param $consultationId
         * @return Response
         */
        public function show($consultationId)
        {
            $validator = Validator::make(['uuid' => $consultationId], [
                'uuid' => 'required|exists:tenant.consultations,uuid',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            $consultation = Consultation::find($consultationId);
            if (!$consultation) {
                return response()->json(['status' => TRUE, 'msg' => 'No Data Found', 'data' => []], 200);
            }
            return response()->json(['status' => TRUE, 'data' => (new ConsultationTransformer($consultation))->additional(['showStep' => TRUE, 'dateFormat' => TRUE])], 200);
        }

        /**
         * Store a newly created resource in storage.
         * @param CreateConsultationRequest $createConsultationRequest
         * @return Response
         */
        public function create(CreateConsultationRequest $createConsultationRequest)
        {
            try {
                DB::connection('tenant')->beginTransaction();
                if ($createConsultationRequest->get('is_reinvent') == 1) {
                    $response = $this->checkReinventOverlap($createConsultationRequest->start_date, $createConsultationRequest->end_date);
                    if ($response) {
                        return $response;
                    }

                }


                $createConsultationRequest['start_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $createConsultationRequest->start_date)));
                $createConsultationRequest['end_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $createConsultationRequest->end_date)));
                $createConsultationRequest['display_results_until'] = date('Y-m-d', strtotime(str_replace('/', '-', $createConsultationRequest->display_results_until)));

                $consultation = Consultation::create([
                    "user_id"               => $createConsultationRequest->get('user_id'),
                    "workshop_id"           => $createConsultationRequest->get('workshop_id'),
                    "name"                  => $createConsultationRequest->get('name'),
                    "internal_name"         => $createConsultationRequest->get('internal_name'),
                    "long_name"             => $createConsultationRequest->get('long_name'),
                    "start_date"            => $createConsultationRequest->get('start_date'),
                    "end_date"              => $createConsultationRequest->get('end_date'),
                    "display_results_until" => $createConsultationRequest->get('display_results_until'),
                    "allow_to_go_back"      => $createConsultationRequest->get('allow_to_go_back'),
                    "is_reinvent"           => $createConsultationRequest->get('is_reinvent'),
                    "public_reinvent"       => $createConsultationRequest->get('is_reinvent') ? $createConsultationRequest->get('public_reinvent') : 0,
                ]);
                DB::connection('tenant')->commit();
                return response()->json(['data' => (new ConsultationTransformer($consultation))->additional(['showReinvent' => FALSE]), 'status' => TRUE], 200);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * Update a resource in storage.
         * @param $consultationId
         * @param UpdateConsultationRequest $updateConsultationRequest
         * @return Response
         */
        public function update($consultationId, UpdateConsultationRequest $updateConsultationRequest)
        {
            $validator = Validator::make(['uuid' => $consultationId], [
                'uuid' => 'required|exists:tenant.consultations,uuid',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            try {
                DB::connection('tenant')->beginTransaction();
                $consultation = Consultation::find($consultationId);

                if ($updateConsultationRequest->has('name')) {
                    $consultation->name = $updateConsultationRequest->get('name');
                }
                if ($updateConsultationRequest->has('internal_name')) {
                    $consultation->internal_name = $updateConsultationRequest->get('internal_name');
                }
                if ($updateConsultationRequest->has('long_name')) {
                    $consultation->long_name = $updateConsultationRequest->get('long_name');
                }
                if ($updateConsultationRequest->has('start_date')) {
                    $updateConsultationRequest['start_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $updateConsultationRequest->start_date)));
                    $consultation->start_date = $updateConsultationRequest->get('start_date');
                    if ($updateConsultationRequest->has('is_reinvent') && $updateConsultationRequest->get('is_reinvent') == 1) {
                        $response = $this->checkReinventOverlap($updateConsultationRequest->start_date, $updateConsultationRequest->end_date, 1, $consultation->uuid);
                        if ($response) {
                            return $response;
                        }
                    }
                }
                if ($updateConsultationRequest->has('end_date')) {
                    $updateConsultationRequest['end_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $updateConsultationRequest->end_date)));
                    $consultation->end_date = $updateConsultationRequest->get('end_date');
                    if ($updateConsultationRequest->has('is_reinvent') && $updateConsultationRequest->get('is_reinvent') == 1) {
                        $response = $this->checkReinventOverlap($updateConsultationRequest->start_date, $updateConsultationRequest->end_date, 1, $consultation->uuid);
                        if ($response) {
                            return $response;
                        }
                    }
                }
                if ($updateConsultationRequest->has('display_results_until')) {
                    $updateConsultationRequest['display_results_until'] = date('Y-m-d', strtotime(str_replace('/', '-', $updateConsultationRequest->display_results_until)));
                    $consultation->display_results_until = $updateConsultationRequest->get('display_results_until');

                }
                if ($updateConsultationRequest->has('allow_to_go_back')) {
                    $consultation->allow_to_go_back = $updateConsultationRequest->get('allow_to_go_back');
                }
                if ($updateConsultationRequest->has('is_reinvent')) {
                    $consultation->is_reinvent = $updateConsultationRequest->get('is_reinvent');
                }
                if ($updateConsultationRequest->has('public_reinvent')) {
                    $consultation->public_reinvent = $updateConsultationRequest->get('is_reinvent') ? $updateConsultationRequest->get('public_reinvent') : 0;
                }
                $consultation->save();
                DB::connection('tenant')->commit();
                return response()->json(['data' => (new ConsultationTransformer($consultation))->additional(['showReinvent' => FALSE]), 'status' => TRUE], 200);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * Store a newly created resource in storage.
         * @param $consultationId
         * @return void
         */
        public function destroy($consultationId)
        {
            $validator = Validator::make(['uuid' => $consultationId], [
                'uuid' => 'required|exists:tenant.consultations,uuid',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            try {
                $delete = Consultation::find($consultationId)->delete();
                if ($delete)
                    return response()->json(['status' => TRUE, 'data' => 'Consultation Deleted Successfully'], 200);
                else
                    return response()->json(['status' => FALSE, 'msg' => 'Invalid Id'], 500);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }

        }

        /**
         * Store a newly created resource in storage.
         * @param UpdateStepRequest $UpdateStepRequest
         * @param $consultationStepId
         * @return Response
         */
        public function updateStep(UpdateStepRequest $updateStepRequest, $consultationStepId)
        {
            $validator = Validator::make(['id' => $consultationStepId], [
                'id' => 'required|exists:tenant.consultation_steps,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            $consultationStep = ConsultationStep::find($consultationStepId);
            if (!$consultationStep) {
                return response()->json(['status' => FALSE, 'msg' => 'Consultation Step id does not exist'], 500);
            }
            try {
                DB::connection('tenant')->beginTransaction();
                if ($updateStepRequest->has('title') && !empty($updateStepRequest->title)) {
                    $consultationStep->title = $updateStepRequest->get('title');
                }
                if ($updateStepRequest->has('description') && !empty($updateStepRequest->description)) {
                    $consultationStep->description = $updateStepRequest->get('description');
                }
                if ($updateStepRequest->get('step_type') === "2") {
                    if ($updateStepRequest->has('answerable')) {
                        $consultationStep->answerable = (int)$updateStepRequest->answerable;
                    }
                    if ($updateStepRequest->has('active')) {
                        $consultationStep->active = (int)$updateStepRequest->active;
                    }
                    if ($updateStepRequest->hasFile('instruction_pdf')) {
                        $file = $this->uploadImageGetUrl($updateStepRequest->instruction_pdf, str_replace(' ', '_', $updateStepRequest->get('title')) . '/instructionPdf');
                        $consultationStep->extra_fields = json_encode([
                            'instruction_pdf' => $file,
                        ]);
                    }
                }
                if ($updateStepRequest->get('step_type') === "3" && (isset($updateStepRequest->meeting_id) && !empty($updateStepRequest->meeting_id))) {
                    if (isset($consultationStep->consultationSprint->consultation->uuid)) {
                        ConsultationStepMeeting::updateOrCreate(
                            [
                                "consultation_step_id" => $consultationStep->id,
                                "consultation_uuid"    => $consultationStep->consultationSprint->consultation->uuid,
                            ], [
                                "consultation_step_id" => $consultationStep->id,
                                "meeting_id"           => $updateStepRequest->meeting_id,
                                "consultation_uuid"    => $consultationStep->consultationSprint->consultation->uuid,
                            ]
                        );
                    }
                }
                if ($updateStepRequest->get('step_type') === "4") {
                    $extraFields = json_decode($consultationStep->extra_fields);
                    $fields['title'] = $updateStepRequest->has('video_title') ? $updateStepRequest->get('video_title') : $extraFields->title;
                    $fields['link'] = $updateStepRequest->has('video_link') ? $updateStepRequest->get('video_link') : $extraFields->link;
                    foreach ($fields as $k => $v) {
                        $extraFields->{$k} = $v;
                    }
                    $consultationStep->extra_fields = json_encode($extraFields);
                }
                if ($updateStepRequest->get('step_type') === "5") {
                    $extraFields = json_decode($consultationStep->extra_fields);
                    $type = $updateStepRequest->has('report_type') ? $updateStepRequest->get('report_type') : $extraFields->report_type;
                    $fields['title'] = $updateStepRequest->has('report_title') ? $updateStepRequest->get('report_title') : $extraFields->title;
                    $fields['link'] = $updateStepRequest->has('report_file') ? $this->uploadImageGetUrl($updateStepRequest->report_file, str_replace(' ', '_', $consultationStep->title) . '/pdf', 'private', $updateStepRequest->get('step_type')) : $extraFields->link;
                    $fields['report_type'] = $type;
                    foreach ($fields as $k => $v) {
                        $extraFields->{$k} = $v;
                    }
                    $consultationStep->extra_fields = json_encode($extraFields);
                }
                $consultationStep->save();
                DB::connection('tenant')->commit();
                return response()->json(['data' => new ConsultationStepTransformer($consultationStep), 'status' => TRUE], 200);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * Store a newly created resource in storage.
         * @param UpdateWelcomeRequest $updateWelcomeRequest
         * @param $consultationStepId
         * @return Response
         */
        public function updateWelcomeStep(UpdateWelcomeRequest $updateWelcomeRequest, $consultationStepId)
        {
            $validator = Validator::make(['id' => $consultationStepId], [
                'id' => 'required|exists:tenant.consultation_steps,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            $consultationStep = ConsultationStep::find($consultationStepId);
            try {
                DB::connection('tenant')->beginTransaction();
                if ((int)$updateWelcomeRequest->get('active') <= 0) {
                    $consultationStep->active = 0;
                } else {
                    if ($updateWelcomeRequest->has('image') && !empty($updateWelcomeRequest->image)) {
                        $image = $this->uploadImageGetUrl($updateWelcomeRequest->image);
                        $consultationStep->image = $image;
                    }
                    if ($updateWelcomeRequest->has('title') && !empty($updateWelcomeRequest->title)) {
                        $consultationStep->title = $updateWelcomeRequest->get('title');
                    }
                    if ($updateWelcomeRequest->has('description') && !empty($updateWelcomeRequest->description)) {
                        $consultationStep->description = $updateWelcomeRequest->get('description');
                    }
                    $consultationStep->active = 1;
                }
                $consultationStep->save();
                DB::connection('tenant')->commit();
                return response()->json(['data' => new ConsultationStepTransformer($consultationStep), 'status' => TRUE], 200);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * Store a newly created resource in storage.
         * @param AddWelcomeRequest $addWelcomeRequest
         * @param ConsultationSprint $consultationSprint
         * @return Response
         */
        public function addWelcomeStep(AddWelcomeRequest $addWelcomeRequest, $consultationSprintId)
        {

            $validator = Validator::make(['id' => $consultationSprintId], [
                'id' => 'required|exists:tenant.consultation_sprints,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            $consultationSprint = ConsultationSprint::find($consultationSprintId);
            // if ($addWelcomeRequest->has('image') && !empty($addWelcomeRequest->image)) {
            //     $image = $this->uploadImageGetUrl($addWelcomeRequest->image);
            // }
            try {
                DB::connection('tenant')->beginTransaction();
                if (!isset($consultationSprint->id) && isset($addWelcomeRequest->consultationSprint)) {
                    $consultationSprint = ConsultationSprint::find($addWelcomeRequest->consultationSprint);
                }
                $welcome = $consultationSprint->consultationStep()->create([
                    "image"       => $addWelcomeRequest->has('image') ? $this->uploadImageGetUrl($addWelcomeRequest->image) : NULL,
                    "title"       => $addWelcomeRequest->active ? $addWelcomeRequest->title : NULL,
                    "description" => $addWelcomeRequest->active ? $addWelcomeRequest->description : NULL,
                    "step_type"   => $addWelcomeRequest->step_type,
                    "active"      => $addWelcomeRequest->active,
                ]);

                DB::connection('tenant')->commit();
                return response()->json(['data' => new ConsultationStepTransformer($welcome), 'status' => TRUE], 200);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * Store a newly created resource in storage.
         * @param AddStepRequest $addStepRequest
         * @param ConsultationSprint $consultationSprint
         * @return Response
         */
        public function addMainStep(AddStepRequest $addStepRequest, $consultationSprintId)
        {
            $validator = Validator::make(['id' => $consultationSprintId], [
                'id' => 'required|exists:tenant.consultation_sprints,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            $consultationSprint = ConsultationSprint::find($consultationSprintId);
            try {
                DB::connection('tenant')->beginTransaction();
                $data = [
                    "title"        => $addStepRequest->get('title'),
                    "description"  => $addStepRequest->get('description'),
                    'step_type'    => $addStepRequest->get('step_type'),
                    'active'       => 1,
                    'extra_fields' => NULL,
                    'sort_order'   => $this->getSortOrder($consultationSprintId),

                ];
                if ($addStepRequest->get('step_type') === "2") {
                    $data['active'] = $addStepRequest->get('active');
                    if ($addStepRequest->hasFile('instruction_pdf')) {
                        $file = $this->uploadImageGetUrl($addStepRequest->instruction_pdf, str_replace(' ', '_', $addStepRequest->get('title')) . '/instructionPdf');
                        $data['extra_fields'] = json_encode([
                            'instruction_pdf' => $file,
                        ]);
                    }
                }
                if ($addStepRequest->get('step_type') === "3") {
                    $step = $consultationSprint->consultationStep()->create($data);
                    ConsultationStepMeeting::create(
                        [
                            "consultation_step_id" => $step->id,
                            "meeting_id"           => $addStepRequest->meeting_id,
                            "consultation_uuid"    => $consultationSprint->consultation->uuid,
                        ]
                    );
                    DB::connection('tenant')->commit();
                    return response()->json(['data' => new ConsultationStepTransformer($step), 'status' => TRUE], 200);
                }
                if ($addStepRequest->get('step_type') === "5") {
                    $file = $this->uploadImageGetUrl($addStepRequest->report_file, str_replace(' ', '_', $addStepRequest->get('title')) . '/pdf', 'private', $addStepRequest->get('step_type'));
                    $type = $addStepRequest->get('report_type');
                    // $image = $this->uploadImageGetUrl($addStepRequest->report_image, str_replace(' ', '_', $addStepRequest->get('title')) . '/mediaImage');
                    $data['extra_fields'] = json_encode([
                        'title'       => $addStepRequest->get('report_title'),
                        'link'        => $file,
                        'report_type' => $type,
                        // 'image' => stripslashes($image),
                    ]);
                }
                if ($addStepRequest->get('step_type') === "4") {
                    $data['extra_fields'] = json_encode([
                        'title' => $addStepRequest->get('video_title'),
                        'link'  => $addStepRequest->get('video_link'),
                    ]);
                }
                $step = $consultationSprint->consultationStep()->create($data);
                DB::connection('tenant')->commit();
                return response()->json(['data' => new ConsultationStepTransformer($step), 'status' => TRUE], 200);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * Store a newly created resource in storage.
         * @param $consultationStepId
         * @return void
         */
        public function deleteStep($consultationStepId)
        {
            $validator = Validator::make(['id' => $consultationStepId], [
                'id' => 'required|exists:tenant.consultation_steps,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            try {
                $step = ConsultationStep::with('stepMeetings')->find($consultationStepId);
                if ($step->step_type === 3) {
                    ConsultationStepMeeting::where('id', $step->stepMeetings->id)->delete();
                }
                $step->delete();
                return response()->json(['status' => TRUE, 'data' => 'Consultation Step Deleted Successfully'], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * Store a newly created resource in storage.
         * @param AddQuestionRequest $addQuestionRequest
         * @param ConsultationStep $consultationStep
         * @return Response
         */
        public function addQuestion(AddQuestionRequest $addQuestionRequest, $consultationStepId)
        {
            $validator = Validator::make(['id' => $consultationStepId], [
                'id' => 'required|exists:tenant.consultation_steps,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            $consultationStep = ConsultationStep::find($consultationStepId);
            try {
                DB::connection('tenant')->beginTransaction();
                $question = $consultationStep->consultationQuestion()->create($addQuestionRequest->all());
                DB::connection('tenant')->commit();
                return response()->json(['status' => TRUE, 'data' => new ConsultationQuestionTransformer($question)], 200);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * Store a newly created resource in storage.
         * @param UpdateThankYouRequest $updateThankYouRequest
         * @param $consultationStepId
         * @return Response
         */
        public function updateThankYouStep(UpdateThankYouRequest $updateThankYouRequest, $consultationStepId)
        {
            $validator = Validator::make(['id' => $consultationStepId], [
                'id' => 'required|exists:tenant.consultation_steps,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            try {
                DB::connection('tenant')->beginTransaction();
                $consultationStep = ConsultationStep::find($consultationStepId);
                if ($updateThankYouRequest->has('title') && !empty($updateThankYouRequest->title)) {
                    $consultationStep->title = $updateThankYouRequest->get('title');
                }
                if ($updateThankYouRequest->has('title_text') && !empty($updateThankYouRequest->title_text)) {
                    $consultationStep->title_text = $updateThankYouRequest->get('title_text');
                }
                $preExtraFields = json_decode($consultationStep->extra_fields);
                $extraFields = [
                    'is_redirection'     => $updateThankYouRequest->is_redirection,
                    'redirect_url'       => isset($preExtraFields->redirect_url) ? $preExtraFields->redirect_url : NULL,
                    'redirect_url_label' => isset($preExtraFields->redirect_url_label) ? $preExtraFields->redirect_url_label : NULL,
                ];
                if ($updateThankYouRequest->is_redirection) {
                    $extraFields['redirect_url'] = $updateThankYouRequest->redirect_url;
                    $extraFields['redirect_url_label'] = $updateThankYouRequest->redirect_url_label;
                }
                $consultationStep->extra_fields = json_encode($extraFields);
                $consultationStep->save();
                DB::connection('tenant')->commit();
                return response()->json(['status' => TRUE, 'data' => new ConsultationStepTransformer($consultationStep)], 200);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * Store a newly created resource in storage.
         * @param AddThankYouRequest $addThankYouRequest
         * @param ConsultationSprint $consultationSprint
         * @return Response
         */
        public function addThankYouStep(AddThankYouRequest $addThankYouRequest, $consultationSprintId)
        {
            $validator = Validator::make(['id' => $consultationSprintId], [
                'id' => 'required|exists:tenant.consultation_sprints,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            $consultationSprint = ConsultationSprint::find($consultationSprintId);
            try {
                DB::connection('tenant')->beginTransaction();

                $thank = $consultationSprint->consultationStep()->create([
                    "title"        => $addThankYouRequest->title,
                    "step_type"    => $addThankYouRequest->step_type,
                    "active"       => 1,
                    "title_text"   => $addThankYouRequest->title_text,
                    "extra_fields" => json_encode([
                        'is_redirection'     => $addThankYouRequest->is_redirection,
                        'redirect_url'       => $addThankYouRequest->is_redirection ? $addThankYouRequest->redirect_url : NULL,
                        'redirect_url_label' => $addThankYouRequest->is_redirection ? $addThankYouRequest->redirect_url_label : NULL,
                    ]),
                ]);
                DB::connection('tenant')->commit();
                return response()->json(['status' => TRUE, 'data' => new ConsultationStepTransformer($thank)], 200);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * Store a newly created resource in storage.
         * @param $stepId
         * @return void
         */
        public function collect($stepId)
        {
            $step = ConsultationStep::with('consultationQuestion')->with('consultationSprint.consultation.workshop')->find($stepId);
            if (!$step) {
                return response()->json(['status' => FALSE, 'msg' => 'Data not found!'], 500);
            }
            $todayDate = Carbon::parse(Carbon::today())->format('Ymd');
            $workshopName = $step->consultationSprint->consultation->workshop->workshop_name;
            $workshopId = $step->consultationSprint->consultation->workshop->id;
            $meta = WorkshopMeta::where(['user_id' => Auth::id(), 'workshop_id' => $workshopId])->first();
            if (!in_array(Auth::user()->role, ['M1', 'M2'])) {
                if (!$meta && $meta->role == 0) {
                    return response()->json(['status' => FALSE, 'msg' => 'member user not authorised!'], 500);
                }
            }
            $fileName = $step->title . '-' . $todayDate . '-' . $workshopName . '.xlsx';

            return Excel::download(new AnswerExport($step->consultationQuestion->whereNotIn('consultation_question_type_id', [17])->sortBy('order')), $fileName);
        }

        /**
         * to get QuestionType Step Questions Data
         *
         */
        public function getStepById($id)
        {
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|exists:tenant.consultation_steps,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            $step = ConsultationStep::has('consultationQuestion')->where(['id' => $id, 'step_type' => 2])->first();
            return (ConsultationQuestionTransformer::collection(isset($step->consultationQuestion) ? $step->consultationQuestion : collect([])));
        }

        public function uploadImageGetUrl($image, $folder = '', $visibility = 'public', $type = 0)
        {
            $imageUrl = '';
            if ($image) {


                $hostname = $this->tenancy->hostname()['fqdn'];
                $domain = strtok($hostname, '.');
                $filePath = $domain . '/consultation' . $folder;
                if ($type == 5) {
                    return $path = \Storage::disk('s3')->putFileAs(
                        $filePath, $image, $image->getClientOriginalName()
                    );
                }
                $imageUrl = ($this->core->fileUploadToS3($filePath, ($image), $visibility));
            }
            return $imageUrl;
        }

        public function getWorkshopMeeting($key, $consultationId, $sprintId)
        {
            $validator = Validator::make(['id' => $consultationId, 'key' => $key, 'sprint_id' => $sprintId], [
                'id'        => 'required|exists:tenant.consultations,uuid',
                'key'       => 'required|min:3',
                'sprint_id' => 'required|exists:tenant.consultation_sprints,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }

            $consultation = Consultation::with(['consultationSprint' => function ($a) use ($sprintId) {
                $a->where('id', $sprintId);
            }, 'stepMeetings'])->where(['uuid' => $consultationId])->first(['uuid', 'start_date', 'workshop_id', 'end_date']);
            $existingMeeting = [];
            if (!empty($consultation->consultationSprint)) {
                $first = collect($consultation->consultationSprint)->first();
                $steps = ConsultationStep::where('consultation_sprint_id', $first->id)->where('step_type', 3)->pluck('id');
                if (!empty($steps)) {
                    $existingMeeting = $consultation->stepMeetings->whereIn('consultation_step_id', $steps)->pluck('meeting_id');
                } else {
                    $existingMeeting = [];
                }
            } else {
                $existingMeeting = [];
            }
            if (isset($consultation->workshop_id)) {

                //  $existingMeeting = [];
                $meetings = Meeting::where('name', 'LIKE', '%' . $key . '%')->where('workshop_id', $consultation->workshop_id)->whereNotIn('id', $existingMeeting)->where(['meeting_type' => 2])->whereDate('date', '>=', $consultation->start_date)->whereDate('date', '<=', date('Y-m-d', strtotime($consultation->end_date)))->whereDate('date', '>=', Carbon::today()->toDateString())->get(['id', 'name', 'workshop_id', 'date', 'start_time']);
                if (!empty($meetings)) {
                    $meetings = collect($meetings)->reject(function ($value, $key) {
                        if ($value->date == Carbon::today()->toDateString()) {
                            return !($value->start_time > Carbon::now()->format('H:i:00'));
                        }
                    });
                    $meetings = $meetings->values();
                }
            } else {
                return response()->json(['status' => FALSE, 'data' => []], 200);
            }
            return response()->json(['status' => TRUE, 'data' => $meetings], 200);
        }

        public function addMeetingStep(StoreMeetingStep $request)
        {
            $consultation = Consultation::find($request->consultation_uuid, ['uuid', 'start_date', 'end_date']);
            $meeting = Meeting::where('id', $request->meeting_id)->whereDate('date', '>=', $consultation->start_date)->whereDate('date', '<=', date('Y-m-d', strtotime($consultation->end_date)))->whereDate('date', '>=', Carbon::today()->toDateString())->get();
            if (!empty($meeting)) {
                $meeting = collect($meeting)->reject(function ($value, $key) {
                    if ($value->date == Carbon::today()->toDateString()) {
                        return !($value->start_time > Carbon::now()->format('H:i:00'));
                    }
                });
                $meeting = $meeting->values();
                if (count($meeting) > 0) {
                    $meetings = ConsultationStepMeeting::create(
                        [
                            "consultation_step_id" => $request->step_id,
                            "meeting_id"           => $request->meeting_id,
                            "consultation_uuid"    => $request->consultation_uuid,
                        ]
                    );
                    return response()->json(['status' => TRUE, 'data' => new MeetingStepResource($meetings)], 200);
                }
                return response()->json(['status' => FALSE, 'msg' => __('message.meeting_date')], 422);
            } else {
                return response()->json(['status' => FALSE, 'msg' => __('message.meeting_date')], 422);
            }
        }

        public function removeMeetingStep($id)
        {
            try {
                $validator = Validator::make(['id' => $id], [
                    'id' => 'required|exists:tenant.consultation_step_meetings,id',
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }

                if (ConsultationStepMeeting::where('id', $id)->delete())
                    return response()->json(['status' => TRUE, 'data' => __('message.DELETED')], 200);
                else
                    return response()->json(['status' => FALSE, 'data' => __('message.NO_DATA')], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        public function getMeetingStep($id)
        {
            try {
                $validator = Validator::make(['id' => $id], [
                    'id' => 'required|exists:tenant.consultation_steps,id',
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                $meetingData = ConsultationStepMeeting::where('consultation_step_id', $id)->get();
                if ($meetingData)
                    return response()->json(['status' => TRUE, 'data' => MeetingStepResource::collection($meetingData)], 200);
                else
                    return response()->json(['status' => FALSE, 'data' => __('message.NO_DATA')], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        public function getWorkshopConsultation($key, $workshopId)
        {
            try {
                $validator = Validator::make(['id' => $workshopId, 'key' => $key], [
                    'id'  => 'required|exists:tenant.workshops,id',
                    'key' => 'required|min:3',
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }

                $workshop = Workshop::withoutGlobalScopes()->find($workshopId);
                $consultations = Consultation::with('consultationSprint:id,consultation_uuid,title')->where('workshop_id', '!=', $workshopId)->where(function ($a) use ($workshop) {
                    if (!$workshop->is_dependent) {
                        $a->orWhereIn('workshop_id', function ($query) use ($workshop) {

                            $query->select('workshops.id')
                                ->from('workshops')
                                ->where(['workshop_type' => 1, 'is_dependent' => 1, 'code1' => $workshop->code1]);
                        });
                    }
                })->groupBy('consultations.uuid')->where(function ($a) use ($key) {
                    $a->orWhere('name', 'LIKE', '%' . $key . '%')->orWhere('internal_name', 'LIKE', '%' . $key . '%');
                })->whereDate('end_date', '>=', Carbon::today()->format('Y-m-d'))->get(['uuid', 'workshop_id', 'name', 'internal_name']);

                return response()->json(['status' => TRUE, 'data' => $consultations], 200);

            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        public function searchConsultation($tense, $key, $paginate = NULL)
        {
            $operator = '>';
            try {
                $consultation = [];
                if (strlen($key) >= 3) {
                    $key = ltrim($key);
                    $key = rtrim($key);
                    $consultation = Consultation::/*where(function ($query) use ($operator) {
                        $query->where('created_at', $operator, date('Y-m-d'))
                            ->orWhere([['start_date', '=', date('Y-m-d')], ['start_date', '=', date('Y-m-d')]]);
                    })->*/ where(function ($query) use ($key) {
                        $query->orWhere(function ($query) use ($key) {
                            $query->orWhere(DB::raw("LOWER(name)"), 'like', strtolower("%$key%"));
                            $query->orWhere(DB::raw("LOWER(created_at)"), 'like', strtolower("%$key%"));
                            $query->orWhere(DB::raw("LOWER(start_date)"), 'like', strtolower("%$key%"));
                        });
                    })
                        ->select('id', 'name', 'start_date', 'end_date', 'workshop_id')
                        ->selectRaw("DATE_FORMAT(consultations.start_date, '%M %d,%Y') as start_date");
                    if ($paginate && $paginate > 0) {
                        return (ConsultationTransformer::collection($consultation->paginate($paginate)))->additional(['showStep' => FALSE, 'dateFormat' => TRUE]);
                    }
                }
                return response()->json(['status' => TRUE, 'data' => (ConsultationTransformer::collection($consultation->get()))->additional(['showStep' => FALSE, 'dateFormat' => TRUE])], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'data' => 'Internal server error in getting Consultation list' . $e->getMessage()], 500);
            }
        }

        public function getPrivateFile($id)
        {
            try {
                $validator = Validator::make(['id' => $id], [
                    'id' => 'required|exists:tenant.consultation_steps,id',
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                $consultationStep = ConsultationStep::where(['id' => $id, 'step_type' => 5])->first(['id', 'extra_fields']);
                if (!empty($consultationStep->extra_fields)) {
                    $decode = json_decode($consultationStep->extra_fields, TRUE);
                    if (isset($decode['link'])) {
                        $file = $this->core->getPrivateAsset($decode['link'], 60);
                        return response()->json(['status' => TRUE, 'data' => $file], 200);
                    } else {
                        return response()->json(['status' => FALSE, 'data' => 'No Record Found.'], 200);
                    }
                } else {
                    return response()->json(['status' => FALSE, 'data' => 'No Record Found.'], 200);
                }

            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        public function getAuthToken()
        {
            if (!Auth::check()) {
                return response()->json([
                    'status' => FALSE,
                    'msg'    => 'Please Login First',
                    'data'   => [
                    ],
                ], 401);
            }

            $user = Auth::user();
            $token_result = $user->createToken('Personal Access Token');
            $token = $token_result->token;
            $token->expires_at = now()->addHours(12);
            $token->save();
            return response()->json([
                'status' => TRUE,
                'data'   => [
                    'access_token' => $token_result->accessToken,
                ],
            ], 200);
        }

        public function testMail(Request $request)
        {
            $userData['fname'] = $request->user()->fname;
            $userData['lname'] = $request->user()->lname;
            $userData['email'] = $request->user()->email;
            $tenancy = app(\Hyn\Tenancy\Environment::class);
            $hostname = $tenancy->hostname()['fqdn'];

            $data = ResilienceService::getInstance()->prepareEmailData((object)$userData, $request->workshop_id, $request->key, Consultation::find($request->uuid), $hostname);

            $lang = session()->has('lang') ? session()->get('lang') : "FR";
            $type = ($lang == 'FR') ? $request->key . '_FR' : $request->key . '_EN';
            $data['mail']['email'] = $request->user()->email;
            $data['mail']['firstname'] = $request->user()->fname;
            $data['mail']['otp'] = 2020;
            $data['mail']['path'] = 'lll';
            $data['mail']['type'] = $type;
            $data['mail']['candidateId'] = 20;
            $core = app(CoreController::class);
            dd($core->SendEmail($data, 'resilience_verification_email'));
        }

        public function checkReminder()
        {

            dump(shell_exec('php artisan mail:consultation-reminder'));
            dd(shell_exec('php artisan mail:consultation-opening-reminder'));
        }

        protected function checkReinventOverlap($start_date, $end_date, $edit = 0, $consultationUuid = NULL)
        {

            $start_date = date('Y-m-d', strtotime(str_replace('/', '-', $start_date)));
            $end_date = date('Y-m-d', strtotime(str_replace('/', '-', $end_date)));
            $overlap = Consultation::where('is_reinvent', 1)->where(function ($query) use ($start_date, $end_date) {
                //$query->where([['start_date', '>=', $start_date], ['end_date', '<=', $end_date]]);
                $query->orWhere([['start_date', '<=', $start_date], ['end_date', '>=', $start_date]]);
                $query->orWhere([['start_date', '<=', $end_date], ['end_date', '>=', $end_date]]);
            });
            if ($edit && !empty($consultationUuid)) {
                $overlap->where('uuid', '!=', $consultationUuid);
            }
            if ($overlap->count() > 0) {
                return response()->json(['status' => FALSE, 'msg' => 'We Already Have Active Consultation in above Period.'], 200);
            }
        }


        public function dragOrder(UpdateSprintStepQuestionOrderRequest $request)
        {
            $data = json_decode($request->data);
            if (count($data) > 0) {
                $type = $this->decideModelForOrder($request->type);
                if ($request->type == 'sprint') {
                    $type->where('consultation_uuid', $request->consultation_uuid);
                } elseif ($request->type == 'step') {
                    $type->where('consultation_sprint_id', $request->sprint_id);
                } elseif ($request->type == 'question') {
                    $type->where('consultation_step_id', $request->step_id);
                }

                foreach ($data as $k => $val) {
                    $type->where('id', $val->id)->update(['sort_order' => $val->sort_order]);
                }
                return $this->sidebar($request->consultation_uuid);
            }
        }

        protected function decideModelForOrder($type)
        {
            switch ($type) {
                case 'sprint':
                    $instance = new ConsultationSprint;
                    break;
                case 'step':
                    $instance = new ConsultationStep;
                    break;
                case 'question':
                    $instance = new ConsultationQuestion;
                    break;
                default:
                    $instance = new ConsultationStep;
            }
            return $instance;
        }

        /**
         * @param int $sprintId
         * @return int
         */
        protected function getSortOrder($sprintId = 1)
        {
            $sortOrder = ConsultationStep::where('consultation_sprint_id', $sprintId)->orderByRaw('CAST(sort_order AS UNSIGNED) desc')->first(['sort_order']);
            if (isset($sortOrder->sort_order)) {
                return ($sortOrder->sort_order + 1);
            } else {
                return 0;
            }
            // return (ConsultationStep::where('consultation_sprint_id', $sprintId)->count() + 1);
        }
    }
