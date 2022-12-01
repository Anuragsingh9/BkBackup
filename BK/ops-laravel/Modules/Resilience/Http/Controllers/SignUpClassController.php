<?php

    namespace Modules\Resilience\Http\Controllers;

    use App\Setting;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Routing\Controller;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Validation\Rule;
    use Modules\Resilience\Entities\ConsultationSignUpClass;
    use Modules\Resilience\Entities\ConsultationSignUpClassPosition;
    use Modules\Resilience\Http\Requests\AddClassRequest;
    use Modules\Resilience\Http\Requests\CheckClassUuid;
    use Modules\Resilience\Http\Requests\DeleteClass;
    use Modules\Resilience\Http\Requests\UpdateClass;
    use Modules\Resilience\Http\Requests\UpdateClassOrderRequest;
    use Modules\Resilience\Transformers\SignUpClassResource;
    use Validator;

    class SignUpClassController extends Controller
    {
        /**
         * @var array
         */
        protected $CLASS_TYPE = [
            'union'   => 1,
            'company' => 2,
        ];
        const NO_DATA = 'resilience::message.no_data';
        const CAST = 'CAST(sort_order AS UNSIGNED) ASC';
        /**
         * @var array
         */
        protected $classLabel = [
            "signUpLeftPageDescription", "signUpLeftPageTitleLine1", "signUpLeftPageTitleLine2", "signUpWelcomeTextLine1", "signUpWelcomeTextLine2",
        ];

        /**
         * Display a listing of the resource.
         * @return Response
         */
        public function index($type = NULL)
        {

            if (empty($type)) {
                $classes = ConsultationSignUpClass::with(['positions' => function ($a) {
                    $a->select('id', 'consultation_sign_up_class_uuid', 'positions', 'sort_order')->orderByRaw(self::CAST);
                }])->orderByRaw(self::CAST)->get();
            } elseif (!in_array(strtolower($type), config('resilience.class_type'))) {
                return response()->json(['status' => FALSE, 'msg' => __(self::NO_DATA), 'data' => []], 200);
            } else {
                $classes = ConsultationSignUpClass::with(['positions' => function ($a) {
                    $a->select('id', 'consultation_sign_up_class_uuid', 'positions', 'sort_order')->orderByRaw(self::CAST);
                }])->where('class_type', $this->CLASS_TYPE[$type])->orderByRaw(self::CAST)->get();
            }
            return SignUpClassResource::collection($classes);
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
         * @param Request $request
         * @return Response
         */
        public function store(AddClassRequest $request)
        {
            try {
                DB::connection('tenant')->beginTransaction();
                $class = ConsultationSignUpClass::create(
                    [
                        'label'         => $request->label,
                        'label_setting' => config('resilience.default_class_label'),
                        'class_type'    => $this->CLASS_TYPE[$request->class_type],
                        'sort_order'    => $this->getSortOrder($this->CLASS_TYPE[$request->class_type]),
                    ]
                );
                DB::connection('tenant')->commit();
                return new SignUpClassResource($class);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * Show the specified resource.
         * @return Response
         */
        public function show($id)
        {
            $class = ConsultationSignUpClass::where('uuid', $id)->first();
            if (!$class) {
                return response()->json(['status' => FALSE, 'msg' => __(self::NO_DATA), 'data' => []], 200);
            }
            return (new SignUpClassResource($class))->additional(['showAll' => TRUE]);

        }

        /**
         * Show the form for editing the specified resource.
         * @return Response
         */
        public function edit()
        {
            return view('resilience::edit');
        }

        /**
         * Update the specified resource in storage.
         * @param Request $request
         * @return Response
         */
        public function update(UpdateClass $request, $id)
        {
            try {
                $class = ConsultationSignUpClass::findOrFail($id);

                DB::connection('tenant')->beginTransaction();
                $classData['label'] = $request->label;

                if ($class->update($classData)) {
                    DB::connection('tenant')->commit();
                    return new SignUpClassResource(ConsultationSignUpClass::find($id));
                } else {
                    DB::connection('tenant')->rollback();
                    return response()->json(['status' => FALSE, 'msg' => __('resilience::message.record_not_update')], 500);
                }
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * Remove the specified resource from storage.
         * calling another function to shift positions
         * @return Response
         */
        public function destroy(DeleteClass $request, $id)
        {
            try {
                $delete = ConsultationSignUpClass::where('uuid', $id);
                $this->moveClassPositions($id, $delete->first());
                $delete = $delete->delete();
                if ($delete) {
                    return response()->json(['status' => TRUE, 'data' => ''], 200);
                } else {
                    return response()->json(['status' => FALSE, 'msg' => 'Invalid Id'], 500);
                }
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }


        /**
         * @param int $type
         * @return int
         * this function is used to get Sort order of new class based on type
         */
        protected function getSortOrder($type = 1)
        {
            return (ConsultationSignUpClass::where('class_type', $type)->count() + 1);
        }


        /**
         * @param UpdateClassOrderRequest $request
         * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
         * this function update the sort order of classes
         */
        public function classDragOrder(UpdateClassOrderRequest $request)
        {
            $data = json_decode($request->data);
            if (count($data) > 0) {
                foreach ($data as $k => $val) {
                    ConsultationSignUpClass::where('uuid', $val->id)->update(['sort_order' => ($k + 1)]);
                }

                return SignUpClassResource::collection(ConsultationSignUpClass::where('class_type', $this->CLASS_TYPE[$request->class_type])->orderByRaw(self::CAST)->get());

            }
        }

        /**
         * @param $id
         * @param $class
         * this function is used to shift all postions of deleting class to
         * first default class based on type
         */
        protected function moveClassPositions($id, $class)
        {
            $name = strtolower(config('resilience.default_class_name'));
            $regularClass = ConsultationSignUpClass::where('class_type', $class->class_type)->whereRaw("LOWER(label)='$name'")->first();
            if (isset($regularClass->uuid)) {
                ConsultationSignUpClassPosition::where('consultation_sign_up_class_uuid', $id)->update(['consultation_sign_up_class_uuid' => $regularClass->uuid]);
            }
        }

        /**
         * @param CheckClassUuid $request
         * @param $uuid
         * @return \Illuminate\Http\JsonResponse
         * this function is used to get class labels
         */
        public function getLabelSetting(CheckClassUuid $request, $uuid)
        {
            $labels = ConsultationSignUpClass::where('uuid', $uuid)->first(['uuid', 'label_setting']);

            $setting = $this->parseLabelSetting($labels->label_setting);

            return response()->json(['status' => TRUE, 'data' => $setting], 200);
        }

        /**
         * @param CheckClassUuid $request
         * @param $uuid
         * @return \Illuminate\Http\JsonResponse
         * this function  update class labels
         */
        public function updateLabel(CheckClassUuid $request, $uuid)
        {
            try {
                DB::connection('tenant')->beginTransaction();
                $updateArr = [];
                foreach ($request->all() as $k => $v) {
                    if (in_array($k, $this->classLabel)) {
                        $updateArr[$k] = $v;
                    }
                }

                $class = ConsultationSignUpClass::where('uuid', $uuid);
                $class->update(['label_setting' => json_encode($updateArr)]);
                DB::connection('tenant')->commit();
                $labels = $class->first(['uuid', 'label_setting']);
                $setting = $this->parseLabelSetting($labels->label_setting);
                return response()->json(['status' => TRUE, 'data' => $setting], 200);

            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        /**
         * @param CheckClassUuid $request
         * @param $uuid
         * @return \Illuminate\Http\JsonResponse
         * as we need to send some extra data so we need to
         * implement controller based validation
         */
        public function getClassSignUpData($uuid)
        {
            $validator = Validator::make(['uuid' => $uuid], [
                'uuid' => ['required', Rule::exists('tenant.consultation_signup_classes', 'uuid')->where(function ($q) {
                    $q->whereNull('deleted_at');
                })],
            ]);
            if ($validator->fails()) {
                return $this->sendErrorResponse();
            }

            $setting = Setting::where('setting_key', "resilience_signup_page")->first();
            if (!$setting) {
                return response()->json(['status' => FALSE, 'msg' => __(self::NO_DATA), 'data' => []], 200);
            }
            //getting default value
            $settingValue = $this->parseLabelSetting($setting->setting_value);
            //getting class value
            $labels = ConsultationSignUpClass::with(['positions' => function ($a) {
                $a->select('id', 'consultation_sign_up_class_uuid', 'positions', 'sort_order')->orderByRaw(self::CAST);
            }])->where('uuid', $uuid)->first(['uuid', 'label_setting']);

            $parsedlabel = $this->parseLabelSetting($labels->label_setting);
            $response['labels'] = (collect($settingValue)->merge($parsedlabel));
            $response['positions'] = ($labels->positions) ? $labels->positions : [];

            return response()->json(['data' => $response['labels'], 'positions' => $response['positions'], 'status' => TRUE], 200);
        }

        /**
         * @param $value
         * @return array|mixed|string|string[]|null
         * this function just decode the values and send response
         */
        protected function parseLabelSetting($value)
        {
            if (is_array($value)) {
                $setting = !empty($value) ? ($value) : [];
            } elseif (isJson($value)) {
                $setting = json_decode($value);
            } else {
                $setting = !empty($value) ? json_decode(preg_replace('/\s+/', '', $value)) : [];
            }
            return $setting;
        }

        /**
         * @return \Illuminate\Http\JsonResponse
         * this function we used to send default class data when user enters wrong ids
         */
        protected function sendErrorResponse()
        {
            $setting = Setting::where('setting_key', "resilience_signup_page")->first();
            //getting class value
            $labels = ConsultationSignUpClass::with(['positions' => function ($a) {
                $a->select('id', 'consultation_sign_up_class_uuid', 'positions', 'sort_order')->orderByRaw(self::CAST);
            }])->where('class_type', 1)->first(['uuid', 'label_setting']);
            //getting default value
            $settingValue = $this->parseLabelSetting($setting->setting_value);
            $parsedlabel = $this->parseLabelSetting($labels->label_setting);
            $response['labels'] = (collect($settingValue)->merge($parsedlabel));
            $response['positions'] = ($labels->positions) ? $labels->positions : [];
            return response()->json(['data' => $response['labels'], 'positions' => $response['positions'], 'status' => TRUE, 'errorMsg' => __('resilience::message.class_not_found')], 200);
        }
    }
