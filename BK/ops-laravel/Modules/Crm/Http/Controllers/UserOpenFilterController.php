<?php
    
    namespace Modules\Crm\Http\Controllers;
    
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Routing\Controller;
    use Illuminate\Support\Facades\Auth;
    use Modules\Crm\Entities\CrmFilter;
    use Modules\Crm\Entities\CrmFilterType;
    use Modules\Crm\Entities\UserOpenFilter;
    use Modules\Crm\Http\Requests\UserOpenFilterRequest;
    use Modules\Crm\Services\CrmServices;
    
    /**
     * Class UserOpenFilterController
     * @package Modules\Crm\Http\Controllers
     */
    class UserOpenFilterController extends Controller
    {
        /**
         * UserOpenFilterController constructor.
         */
        private $crmService;
        
        public function __construct(CrmServices $crmServices)
        {
            $this->crmService = $crmServices;
        }
        
        /**
         * Display a listing of the resource.
         * @return Response
         */
        public function index()
        {
            return view('crm::index');
        }
        
        /**
         * Show the form for creating a new resource.
         * @return Response
         */
        public function create()
        {
            return view('crm::create');
        }
        
        /**
         * Store a newly created resource in storage.
         * @param Request $request
         * @return Response
         */
        public function store(UserOpenFilterRequest $request)
        {
            try {
                
                $already = UserOpenFilter::where([
                    'user_id' => Auth::user()->id,
                    'filter_type_id' => $request->filter_type_id,
                ])->first(['id', 'user_id', 'filter_type_id', 'filter_id']);
                if (isset($already->user_id)) {
                    $update = [
                        'filter_type_id' => $request->filter_type_id,
                        'user_id'        => Auth::user()->id,
                    ];
                    if (($request->filter_type_id == $already->filter_type_id)) {
                        if (!empty($request->filter_id)) {
                            $update['filter_id'] = isset($request->filter_id) ? $request->filter_id : NULL;
                        }
                    } else {
                        $update['filter_id'] = isset($request->filter_id) ? $request->filter_id : NULL;
                    }
                    $filter = UserOpenFilter::where(
                        [
                            'user_id' => Auth::user()->id,
                            'filter_type_id' => $request->filter_type_id,
                        ])->update($update);
                } else {
                    $filter = UserOpenFilter::create(
                        [
                            'filter_type_id' => $request->filter_type_id,
                            'filter_id'      => isset($request->filter_id) ? $request->filter_id : NULL,
                            'user_id'        => Auth::user()->id,
                        ]
                    );
                }
                return $response = $this->responseFilter($request->filter_type_id,Auth::user()->id);
//                return response()->json(['status' => TRUE, 'data' => $response], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
            
        }
        
        /**
         * Show the specified resource.
         * @return Response
         */
        public function show()
        {
            return view('crm::show');
        }
        
        /**
         * Show the form for editing the specified resource.
         * @return Response
         */
        public function edit()
        {
            return view('crm::edit');
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
         * Send the filter for User saved  Object or For Default .
         * @param $user
         * @return Response
         */
        public function responseFilter($filterTypeId = NULL, $user = NULL)
        {
            try {
                if (empty($user)) {
                    $user = Auth::user()->id;
                }
                //setting up Default values For @$filterTypeId and @$filterId
                if (empty($filterTypeId))
                $filterTypeId = CrmFilterType::first()->id;
                //checking if user have any saved Filter
                $userFilter = UserOpenFilter::where(['user_id' => $user, 'filter_type_id' => $filterTypeId])->first(['id', 'user_id', 'filter_type_id', 'filter_id']);
//                if (isset($userFilter->filter_type_id)) {
//                    $filterTypeId = $userFilter->filter_type_id;
//                }
                
                if (!empty($userFilter->filter_id))
                    $filterId = $userFilter->filter_id;
                else
                    $filterId = CrmFilter::where(['filter_type_id' => $filterTypeId, 'is_default' => 1])->first()->id;
                //get filters for specific object
                $filters = $this->crmService->listFilters($filterTypeId);
                return response()->json(['status' => TRUE, 'data' => ['filters' => $filters, 'filter_type_id' => $filterTypeId, 'filter_id' => $filterId]], 200);
            } catch (\Exception $e) {
                
                throw new \Exception($e->getMessage());
                // return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
    }
