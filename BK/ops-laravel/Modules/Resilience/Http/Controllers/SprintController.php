<?php

    namespace Modules\Resilience\Http\Controllers;

    use App\Http\Controllers\CoreController;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Routing\Controller;
    use Modules\Resilience\Entities\ConsultationSprint;
    use Modules\Resilience\Http\Requests\StoreConsultationSprint;
    use DB;
    use Modules\Resilience\Http\Requests\UpdateConsultationSprint;
    use Modules\Resilience\Transformers\ConsultationStepTransformer;
    use Modules\Resilience\Transformers\SprintCollection;
    use Modules\Resilience\Transformers\SprintResource;
    use Validator;

    class SprintController extends Controller
    {
        /**
         * SprintController constructor.
         */
        private $core;

        public function __construct()
        {
            $this->core = app(CoreController::class);
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);
        }

        /**
         * Display a listing of the resource.
         * @return Response
         */
        public function index()
        {
            return view('resilience::index');
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
         * Store a newly created resource in storage.
         * @param StoreConsultationSprint $request
         * @return Response
         */
        public function store(StoreConsultationSprint $request)
        {
            try {
                DB::connection('tenant')->beginTransaction();

                $imageSelected = $this->uploadImageGetUrl($request->image_selected);
                $imageNonSelected = $this->uploadImageGetUrl($request->image_non_selected);
                $firstSprint = $this->checkFirstSprint($request->consultation_uuid);
                $sprint = ConsultationSprint::create(
                    [
                        'title'              => $request->title,
                        'description_1'      => $request->description_1,
                        'description_2'      => $request->description_2,
                        'description_3'      => $request->description_3,
                        'image_non_selected' => $imageNonSelected,
                        'image_selected'     => $imageSelected,
                        'consultation_uuid'  => $request->consultation_uuid,
                        'sort_order'         => $this->getSortOrder($request->consultation_uuid),
                        'is_accessible'      => ($firstSprint == 0) ? 1 : $request->is_accessible,
                    ]
                );
                //
                if ($sprint->is_accessible) {
                    $this->updateSprintIsAccessible($request->consultation_uuid, $sprint->id);
                }
                DB::connection('tenant')->commit();
                return new SprintResource($sprint);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * Show the specified sprints resource
         * @return Response
         */
        public function show($id)
        {
            $sprint = ConsultationSprint::has('consultationStep')->where('id', $id)->first();
            if (!$sprint) {
                return response()->json(['status' => FALSE, 'msg' => 'No Data Found', 'data' => []], 200);
            }
            return (ConsultationStepTransformer::collection(isset($sprint->consultationStep) ? $sprint->consultationStep : collect([])));

        }

        /**
         * Show the form for editing the specified resource.
         * @return Response
         */
        public function edit($id)
        {
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|exists:tenant.consultation_sprints,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            return (new SprintResource(ConsultationSprint::where('id', $id)->first()))->additional(['showStepData' => FALSE]);
        }

        /**
         * Update the specified resource in storage.
         * @param Request $request
         * @return Response
         */
        public function update(UpdateConsultationSprint $request, $id)
        {
            try {
                $sprint = ConsultationSprint::find($id);
                if (!$sprint) {
                    return response()->json(['status' => FALSE, 'msg' => "Record Not Found"], 404);
                }
                DB::connection('tenant')->beginTransaction();
                if ($request->has('title') && !empty($request->title)) {
                    $sprintData['title'] = $request->title;
                }
                if ($request->has('description_1') && !empty($request->description_1)) {
                    $sprintData['description_1'] = $request->description_1;
                }
                if ($request->has('description_2') && !empty($request->description_2)) {
                    $sprintData['description_2'] = $request->description_2;
                }
                if ($request->has('description_3') && !empty($request->description_3)) {
                    $sprintData['description_3'] = $request->description_3;
                }
                if ($request->has('is_accessible')) {
                    $sprintData['is_accessible'] = $request->is_accessible;
                    //
                    if ($request->is_accessible) {
                        $this->updateSprintIsAccessible($request->consultation_uuid, $sprint->id);
                    }
                }
                if ($request->has('image_selected') && !empty($request->image_selected)) {
                    $imageSelected = $this->uploadImageGetUrl($request->image_selected);
                    $sprintData['image_selected'] = $imageSelected;
                }
                if ($request->has('image_non_selected') && !empty($request->image_non_selected)) {
                    $imageNonSelected = $this->uploadImageGetUrl($request->image_non_selected);
                    $sprintData['image_non_selected'] = $imageNonSelected;
                }
                if ($sprint->update($sprintData)) {
                    DB::connection('tenant')->commit();
                    return new SprintResource(ConsultationSprint::find($id));
                } else {
                    DB::connection('tenant')->rollback();
                    return response()->json(['status' => FALSE, 'msg' => "Record Not Updated"], 500);
                }
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * Remove the specified resource from storage.
         * @return Response
         */
        public function destroy($id)
        {
            try {
                $validator = Validator::make(['id' => $id], [
                    'id' => 'required|exists:tenant.consultation_sprints,id',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                $this->setIsAccessibleOnDelete($id);
                $delete = ConsultationSprint::whereId($id)->delete();
                if ($delete)
                    return response()->json(['status' => TRUE, 'data' => ''], 200);
                else
                    return response()->json(['status' => FALSE, 'msg' => 'Invalid Id'], 500);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }

        }

        public function uploadImageGetUrl($image, $folder = '', $visibility = 'public')
        {
            $imageUrl = '';
            if ($image) {
                $hostname = $this->tenancy->hostname()['fqdn'];
                $domain = strtok($hostname, '.');
                $filePath = $domain . '/consultation' . $folder;
                $imageUrl = ($this->core->fileUploadToS3($filePath, ($image), $visibility));
            }
            return $imageUrl;
        }

        public function meetingStep($id)
        {
            $sprint = ConsultationSprint::with(['consultationStep' => function ($a) {
                $a->where('step_type', 3);
            }])->where('id', $id)->first();
            if (!$sprint) {
                return response()->json(['status' => FALSE, 'msg' => 'No Data Found', 'data' => []], 200);
            }
            return (ConsultationStepTransformer::collection(isset($sprint->consultationStep) ? $sprint->consultationStep : collect([])));
        }

        /*
         * this function update is_accessible to 0 for all sprint of
         * a consultation while we set is_accessible to 1 for any
         * sprint of consultation
         * */
        protected function updateSprintIsAccessible($consultation, $id): int
        {
            return ConsultationSprint::where(['consultation_uuid' => $consultation])->where('id', '!=', $id)->update(['is_accessible' => 0]);
        }

        /*
         * this function to check first sprint of
         * a consultation while we create sprint is_accessible to 1 for any
         * sprint of consultation
         * */
        protected function checkFirstSprint($consultation)
        {
            return ConsultationSprint::where(['consultation_uuid' => $consultation])->count();
        }

        /**
         * @param int $uuid
         * @return int
         */
        protected function getSortOrder($uuid = 1)
        {
            $sortOrder = ConsultationSprint::where('consultation_uuid', $uuid)->orderByRaw('CAST(sort_order AS UNSIGNED) desc')->first(['sort_order']);
            if (isset($sortOrder->sort_order)) {
                return ($sortOrder->sort_order + 1);
            } else {
                return 0;
            }
            // return (ConsultationSprint::where('consultation_uuid', $uuid)->count() + 1);
        }

        protected function setIsAccessibleOnDelete($id)
        {
            $sprint = ConsultationSprint::whereId($id)->first();
            if ($sprint->is_accessible) {
                $topOrder = ConsultationSprint::where('consultation_uuid', $sprint->consultation_uuid)->where('sort_order', '>', $sprint->sort_order)->first(['id', 'sort_order']);
                $minOrder = ConsultationSprint::where('consultation_uuid', $sprint->consultation_uuid)->where('sort_order', '<', $sprint->sort_order)->orderByRaw('CAST(sort_order AS UNSIGNED) desc')->first(['id', 'sort_order']);

                if (isset($minOrder->sort_order)) {
                    $newId = $minOrder->id;
                } elseif (isset($topOrder->sort_order)) {
                    $newId = $topOrder->id;
                }
                $updateLastAccesible = ConsultationSprint::where(['id' => $newId, 'consultation_uuid' => $sprint->consultation_uuid])->update(['is_accessible' => 1]);
                if ($updateLastAccesible) {
                    ConsultationSprint::where('id', '!=', $newId)->where(['consultation_uuid' => $sprint->consultation_uuid])->update(['is_accessible' => 0]);
                }

            }
        }
    }
