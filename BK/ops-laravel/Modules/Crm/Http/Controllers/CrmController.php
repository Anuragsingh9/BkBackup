<?php
    
    namespace Modules\Crm\Http\Controllers;
    
    use App\User;
    use Illuminate\Validation\Rule;
    use Modules\Crm\Entities\CrmFilterField;
    use Modules\Crm\Services\FilterResultService;
    use Validator;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Routing\Controller;
    use Modules\Crm\Entities\CrmFilterType;
    use Modules\Crm\Services\CrmServices;
    use App\Model\Skill;
    use App\Model\SkillTabs;
    
    class CrmController extends Controller
    {
        private $instance, $filterService;
        /**
         * Create a new controller instance.
         *
         * @return void
         */
        public function __construct()
        {
            $this->instance = CrmServices::getInstance();
            $this->filterService = FilterResultService::getInstance();
        }
        
        /**
         * @return mixed
         */
        public function index()
        {
            $filters = $this->instance->listFilters();
            return response()->json(['status' => TRUE, 'data' => $filters->toArray()], 200);
        }
        
        /**
         * @param $id
         * @return mixed
         */
        public function show($id)
        {
            $this->instance->setFiler($id);
            
            if (!$this->instance->hasFilter()) {
                return response()->json(['status' => FALSE, 'msg' => 'Filter not found'], 400);
            }
            return response()->json(['status' => TRUE, 'data' => $this->instance->getFilerData()], 200);
        }
        
        
        /**
         * validate and store filter data
         *
         * @param Request $request
         * @return mixed
         */
        public function store(Request $request)
        {
            $this->instance->validateFilterData($request);
            
            if ($this->instance->validation->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $this->instance->validation->errors()->all())], 400);
            }
            
            $this->instance->previewBeforeSave();
            $response = $this->instance->saveFiler();
            return response()->json(['status' => TRUE, 'filterId' => $response, 'msg' => 'Filter Added Successfully'], 200);
        }
        
        /**
         * validate and preview filter data
         *
         * @param Request $request
         * @return mixed
         */
        public function previewBeforeSave(Request $request)
        {
            $this->instance->validateFilterData($request, NULL, 'preview');
            
            if ($this->instance->validation->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $this->instance->validation->errors()->all())], 400);
            }
            if ($this->instance->previewBeforeSave()) {
                $response = $this->instance->previewResult();
            } else {
                $response = [];
            }
            return response()->json(['status' => TRUE, 'data' => $response, 'msg' => 'Filter view'], 200);
        }
        
        /**
         * @param $id
         * @return mixed
         */
        public function preview(Request $request, $id)
        {
            $this->instance->request = $request;
            $this->instance->setFiler($id);
            
            if (!$this->instance->hasFilter()) {
                return response()->json(['status' => FALSE, 'msg' => 'Filter not found'], 400);
            }
            
            
            return response()->json(['status' => TRUE, 'data' => $this->instance->previewResult()], 200);
        }
        
        /**
         * @param Request $request
         * @param $id
         * @return mixed
         */
        public function update(Request $request, $id)
        {
            $this->instance->setFiler($id);
            if (!$this->instance->hasFilter()) {
                return response()->json(['status' => FALSE, 'msg' => 'Filter not found'], 400);
            }
            $this->instance->validateFilterData($request, $id, 'update');
            if ($this->instance->validation->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $this->instance->validation->errors()->all())], 400);
            }
            $this->instance->previewBeforeSave();
            $response = $this->instance->updateFilter();
            return response()->json(['status' => $response, 'filterId' => $id, 'msg' => 'Filter updated Successfully'], 200);
        }
        
        /**
         *
         */
        public function saveAsNew()
        {
        }
        
        /**
         * Remove the specified resource from storage.
         * @param Request $request
         * @param $id
         * @return Response
         */
        public function destroy(Request $request, $id)
        {
            $this->instance->setFiler($id);
            if (!$this->instance->hasFilter()) {
                return response()->json(['status' => FALSE, 'msg' => 'Filter not found'], 400);
            }
            $response = $this->instance->deleteFilter();
            
            return response()->json(['status' => $response, 'msg' => 'Filter deleted Successfully'], 200);
        }
        
        /**
         * get filter type list from api
         *
         * @return mixed
         */
        public function getFilterTypeList($id=1)
        {
            try {
                $validator = Validator::make(['filter_type_id'=>$id], [
                    'filter_type_id' => ['required', Rule::exists('tenant.crm_filter_types', 'id')],
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422); //validation false return errors
                }
                $list = $this->instance->getFilterType($id);
            return response()->json(['status' => TRUE, 'data' => $list], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function getCustomFieldList($id)
        {
            $filterType = CrmFilterType::find($id);
            if (!$filterType) {
                return response()->json(['status' => FALSE, 'msg' => 'Filter type id not exist'], 400);
            }
            
            if ($data = $this->instance->getCustomFieldList($filterType)) {
                return response()->json(['status' => TRUE, 'data' => $data, 'msg' => 'Filter field view'], 200);
            } else {
                return response()->json(['status' => FALSE, 'msg' => 'Filter type not identified'], 400);
            }
        }
        
        public function addFilterToWorkshop(Request $request)
        {
            
            try {
                $validator = Validator::make($request->all(), [
                    'persons'     => 'required|array',
                    'workshop_id' => ['required', Rule::exists('workshops', 'id')],
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422); //validation false return errors
                }
                $members = $this->filterService->addNewMembers($request->persons, $request->workshop_id);
                return response()->json(['status' => TRUE, 'data' => $members, 'msg' => 'Members added'], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        // added by vijay for search custom field by name
        
        /**
         * @param array $models
         * @param string $key
         * @return array
         */
        public function getCustomFillable($keyword, $type = 0, $id = 0)
        {
            if (!empty($keyword) && strlen($keyword) >= 3) {
                if ($id) {
                    $fields = CrmFilterField::where('filter_id', $id)->first();
                    $value = json_decode($fields->value, TRUE);
                }
                if ($type == 5) {
                    if (config('constants.Press')) {
                    $type = 7;
                    } else {
                        return response()->json(['status' => TRUE, 'data' => []], 200);
                    }
                } elseif ($type == 4) {
                    if (config('constants.Instance')) {
                    $type = 3;
                    } else {
                        return response()->json(['status' => TRUE, 'data' => []], 200);
                    }
                } elseif ($type == 3) {
                    $type = 4;
                }
                
                $custom = isset($value['custom']) ? $value['custom'] : [];
                $keyword = ltrim($keyword);
                $keyword = rtrim($keyword);
                if ($type > 1)
                    $skillsTabs = SkillTabs::where('tab_type', $type)->where('is_valid', 1)->pluck('id');
                else
                    $skillsTabs = SkillTabs::whereIn('tab_type', [0, 1])->where('is_valid', 1)->pluck('id');
                $tabsArray = Skill::whereNotIn('skill_format_id',[7,12,13,14,16,17,18,20,21,22])->whereIn('skill_tab_id', $skillsTabs)->whereNotIn('id', $custom)->where(function ($query) use ($keyword) {
                    $query->orWhere('short_name', 'like', '%' . $keyword . '%')
                        ->orWhere('name', 'like', '%' . $keyword . '%');
                })->where('is_valid', 1)->get(['id', 'short_name', 'name', 'skill_format_id']);
                return response()->json(['status' => TRUE, 'data' => $tabsArray], 200);
            }
        }
        
        public function addFilterField(Request $request, $id)
        {
            $this->instance->setFiler($id);
            if (!$this->instance->hasFilter()) {
                return response()->json(['status' => FALSE, 'msg' => 'Filter not found'], 400);
            }
            $response = $this->instance->addFilterField($request);
            
            return response()->json(['status' => $response, 'msg' => 'Filter updated Successfully'], 200);
        }
        
        public function deleteFilterField(Request $request, $id)
        {
            $this->instance->setFiler($id);
            if (!$this->instance->hasFilter()) {
                return response()->json(['status' => FALSE, 'msg' => 'Filter not found'], 400);
            }
            $response = $this->instance->deleteFilterField($request);
            
            return response()->json(['status' => $response, 'msg' => 'Filter updated Successfully'], 200);
        }
        
        public function updateFilterFieldsValue(Request $request, $id)
        {
            try {
                $this->instance->validateFieldMainData($request);
                if ($this->instance->validation->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $this->instance->validation->errors()->all())], 422);
                }
                
                $this->instance->validateFieldUpdateData($request);
                if ($this->instance->validation->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $this->instance->validation->errors()->all())], 422);
                }
                
                $response = $this->instance->updateCustomFieldsValue();
                return response()->json(['status' => $response, 'msg' => 'Filter record updated Successfully'], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function getFilteredUser(Request $request)
        {
            try {
                //we can send role in the request also so we can change it as per request in future
                //validate data with Validator
                $validator = Validator::make($request->all(), [
                    'val' => 'required|min:3|max:50',
                ]);
                
                //check if validator fails
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 400);
                }
                
                $data = User::where('role', '!=', 'M3')->where(DB::raw('CONCAT(email," ", lname," ",fname)'), 'like', '%' . $request->val . '%')->where(function ($a) {
                    $a->orWhere('permissions->crmAssistance', 1);
                })->groupBy('email')->get();
                return response()->json($data);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
            
        }
    }
