<?php

    namespace Modules\Resilience\Http\Controllers;

    use App\EntityUser;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Routing\Controller;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Validation\Rule;
    use Modules\Resilience\Entities\ConsultationSignUpClass;
    use Modules\Resilience\Entities\ConsultationSignUpClassPosition;
    use Modules\Resilience\Http\Requests\AddClassPositionRequest;
    use Modules\Resilience\Http\Requests\GetClassPositions;
    use Modules\Resilience\Http\Requests\UpdateClassPosition;
    use Modules\Resilience\Http\Requests\UpdateClassPositionOrderRequest;
    use Modules\Resilience\Transformers\SignUpClassPositionResource;


    class SignUpClassPositionController extends Controller
    {

        /**
         * Display a listing of the resource.
         * as this is get request so added the validation here
         * @return Response
         */
        public function index(GetClassPositions $request, $uuid)
        {
            return SignUpClassPositionResource::collection(ConsultationSignUpClassPosition::where('consultation_sign_up_class_uuid', $uuid)->orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->get());
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
        public function store(AddClassPositionRequest $request)
        {
            try {
                DB::connection('tenant')->beginTransaction();
                $classPos = ConsultationSignUpClassPosition::create(
                    [
                        'positions'                       => $request->positions,
                        'consultation_sign_up_class_uuid' => $request->class_uuid,
                        'sort_order'                      => $this->getSortOrder($request->class_uuid),
                    ]
                );
                DB::connection('tenant')->commit();
                return new SignUpClassPositionResource($classPos);
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
            $classPos = ConsultationSignUpClassPosition::where('id', $id)->first();
            if (!$classPos) {
                return response()->json(['status' => FALSE, 'msg' => 'No Data Found', 'data' => []], 200);
            }
            return (new SignUpClassPositionResource($classPos))->additional(['showAll' => TRUE]);

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
        public function update(UpdateClassPosition $request, $id)
        {
            try {
                $classPos = ConsultationSignUpClassPosition::findOrFail($id);

                DB::connection('tenant')->beginTransaction();
                $classPosData['positions'] = $request->positions;

                if ($classPos->update($classPosData)) {
                    DB::connection('tenant')->commit();
                    return new SignUpClassPositionResource(ConsultationSignUpClassPosition::find($id));
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
                $delete = ConsultationSignUpClassPosition::where('id', $id)->delete();
                EntityUser::where('consultation_sign_up_class_positions_id', $id)->update(['entity_label' => NULL, 'consultation_sign_up_class_positions_id' => NULL]);
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
         * @param $class
         * @return int
         */
        protected function getSortOrder($uuid = 1)
        {
            return (ConsultationSignUpClassPosition::where('consultation_sign_up_class_uuid', $uuid)->count() + 1);
        }


        /**
         * @param UpdateClassPositionOrderRequest $request
         * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
         */
        public function classPositionDragOrder(UpdateClassPositionOrderRequest $request)
        {
            $data = json_decode($request->data);
            if (count($data) > 0) {
                foreach ($data as $k => $val) {
                    ConsultationSignUpClassPosition::where('id', $val->id)->update(['sort_order' => ($k + 1)]);
                }

                return SignUpClassPositionResource::collection(ConsultationSignUpClassPosition::where('consultation_sign_up_class_uuid', $request->class_uuid)->orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->get());

            }
        }

        /**
         * @param $uuid
         * @return mixed
         */
        protected function getClassData($uuid)
        {
            return ConsultationSignUpClass::where('uuid', $uuid)->first();
        }
    }
