<?php
    /**
     * Created by PhpStorm.
     * User: Sourabh Pancharia
     * Date: 5/28/2019
     * Time: 12:14 PM
     */

    namespace App\Services;

    use App\Grdp;
    use App\Http\Controllers\WorkshopController;
    use App\Meeting;
    use App\MessageCategory;
    use App\Milestone;
    use App\Project;
    use App\Task;
    use App\User;
    use App\WorkshopCode;
    use App\WorkshopMeta;
    use Illuminate\Support\Facades\DB;
    use App\Workshop;
    use Excel;
    use http\Env\Response;
    use Hyn\Tenancy\Models\Hostname;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Storage;
    use Carbon;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Validation\Rule;
    use Modules\Events\Entities\Event;
    use Modules\Messenger\Service\TopicService;


    class WorkshopService
    {
        /**
         * SuperAdminSingleton constructor.
         */
        private $tenancy, $core, $import;

        public function __construct()
        {
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);
            $this->core = app(\App\Http\Controllers\CoreController::class);
            $this->import = app(\App\Http\Controllers\import\ImportController::class);
        }

        /**
         * Make instance of SuperAdmin singleton class
         * @return WorkshopService|null
         */
        public static function getInstance()
        {
            static $instance = NULL;
            if (NULL === $instance) {
                $instance = new static();
            }
            return $instance;
        }

        public function updateWorkshopGraphics($request)
        {
            $last_rec = Workshop::where('id', $request->workshop_id)->withoutGlobalScopes()->first();/*update($postData)*/
            $graphicWeb = getSettingData('graphic_config', 1);
            if (isset($request->custom_graphics_enable) && $request->custom_graphics_enable == 0) {

                if (!empty($last_rec->setting)) {
                    $last_rec['setting->custom_graphics_enable'] = $request->custom_graphics_enable;
                } else {
                    $last_rec->setting = ['custom_graphics_enable' => $request->custom_graphics_enable];
                }
                $last_rec->save();
                return $last_rec;
            } else {
                $pre_data = $this->getSetting('web', $request->workshop_id);
                $data['custom_graphics_enable'] = 1;
                $data['web'] = ['headerColor1' => json_decode($request->headerColor1), 'headerColor2' => json_decode($request->headerColor2),
                                'color1'       => json_decode($request->color1),
                                'color2'       => json_decode($request->color2),
                                'transprancy1' => json_decode($request->transprancy1),
                                'transprancy2' => json_decode($request->transprancy2),
                ];
                if ($request->hasFile('header_logo')) {
                    $domain = strtok($_SERVER['SERVER_NAME'], '.');
                    $folder = $domain . '/uploads';
                    $data['web']['header_logo'] = $this->core->fileUploadByS3($request->file('header_logo'), $folder, 'public');
                } else {
                    $data['web']['header_logo'] = (!empty($last_rec->setting)) ? @$pre_data->setting['web']['header_logo'] : @$graphicWeb->header_logo;
                }
                if ($request->hasFile('right_header_icon')) {
                    $domain = strtok($_SERVER['SERVER_NAME'], '.');
                    $folder = $domain . '/uploads';
                    $data['web']['right_header_icon'] = $this->core->fileUploadByS3($request->file('right_header_icon'), $folder, 'public');
                } else {
                    $data['web']['right_header_icon'] = (!empty($last_rec->setting)) ? @$pre_data->setting['web']['right_header_icon'] : @$graphicWeb->right_header_icon;
                }
                if ($request->pdf_switch == '1') {
                    $graphicPdf = getSettingData('pdf_graphic');
                    $data['pdf_switch'] = 1;
                    $pre_data = $this->getSetting('pdf', $request->workshop_id);
                    $data['pdf'] = [
                        'color1' => json_decode($request->color1),
                        'color2' => json_decode($request->color2),

                    ];
                    if ($request->hasFile('header_logo')) {
                        $domain = strtok($_SERVER['SERVER_NAME'], '.');
                        $folder = $domain . '/uploads';
                        $data['pdf']['header_logo'] = $this->core->fileUploadByS3($request->file('header_logo'), $folder, 'public');
                    } else {
                        $data['pdf']['header_logo'] = (!empty($last_rec->setting)) ? @$pre_data->setting['pdf']['header_logo'] : @$graphicPdf->header_logo;
                    }
                } else {
                    $data['pdf_switch'] = 0;
                }

                if (isset($data['pdf'])) {
//                var_export($last_rec->setting);exit;
                    $last_rec->setting = ($data);
                } else {
                    $last_rec['setting->web'] = $data['web'];
                    $last_rec['setting->pdf_switch'] = $data['pdf_switch'];
                    $last_rec['setting->custom_graphics_enable'] = $data['custom_graphics_enable'];
                }
                $last_rec->save();

//          /  var_export($last_rec);exit;
                return $last_rec;
            }
        }

        public function getSetting($type, $id)
        {
            return $last_rec = Workshop::where('id', $id)->withoutGlobalScopes()->first(['setting']);
        }


        /**
         * create a default commission for Module wise
         * we use $type for determine that what type of
         * commission we need to create we have commission or workshop.
         * @param int $type
         * @param string $name
         * @param string $code1
         * @param string $code1
         * @param bool $projectEnable
         * @param array $params
         * @return \Illuminate\Http\JsonResponse
         */
        //public function createCommission($sec, $deputy, $name, $desc, $code1, $code2, $type, $private, $projectEnable, $params)
        public function createCommission($params, $type, $extras = [])
        {
            if (!session()->has('superadmin') && in_array($params['code1'], ['NSL', 'CRM'])) {
                return response()->json(['status' => FALSE, 'msg' => "You don't have permission to create commission with code1 =" . $params['code1']], 403);
            }

            $validator = Validator::make($params, [
                'workshop_name' => 'required|string|max:255',
                'workshop_type' => 'required|in:1,2',
                'code1'         => 'required|string|min:2|max:4',
                'code2'         => 'required_if:workshop_type,2|nullable|string',
                'address'       => 'required',
                'is_private'    => 'required|in:1,0',
                'president_id'  => 'required|exists:tenant.users,id',
                'validator_id'  => 'required|exists:tenant.users,id',
            ]);
            //added validation for code1 and code2 unique validation
            $validator->after(function ($validator) use ($params) {

                if ($params['workshop_type'] == 1) {
                    if (WorkshopController::checkUnique(collect($params))) {
                        return response((['status' => 1, 'msg' => 'This Code already exists!']));
                    }
                } else {
                    if (WorkshopController::checkValidCombination(collect($params))) {
                        $validator->errors()->add('code2', 'This Combination already in use.Please Choose a different one!');
                    }
                }
            });
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            $typeData = [];
            switch ($type) {
                case "qualification":
                    $typeData['is_qualification_workshop'] = $extras['is_qualification_workshop'];
                    $typeData['setting'] = json_encode($extras['setting']);
                    $typeData['signatory'] = json_encode($extras['signatory']);
                    break;
                case "event":
                    $typeData['is_event_workshop'] = 1;
                    break;
                default:
            }
            $workshopData = [
                'president_id'              => $params['president_id'],
                'validator_id'              => $params['validator_id'],
                'workshop_name'             => $params['workshop_name'],
                'workshop_desc'             => $params['workshop_desc'],
                'code1'                     => $params['code1'],
                'code2'                     => $params['code2'],
                'workshop_type'             => $params['workshop_type'],
                'is_private'                => $params['is_private'],
                'is_qualification_workshop' => (isset($params['is_qualification_workshop']) ? $params['is_qualification_workshop'] : 0),
            ];
            $workshopData = array_merge($workshopData, $typeData);
            try {

                //start transaction for skip the wrong entry
                DB::connection('tenant')->beginTransaction();
                $codeId = WorkshopCode::insertGetId(['code' => $params['code1']]);
                if (!$codeId) {
                    return null;
                }
                $wid = Workshop::insertGetId($workshopData);
                if ($wid) {
                    //prepare and create workshop code and workshop meta
                    $codeWorkshopLink = WorkshopCode::where('id', $codeId)->update(['workshop_id'=>$wid]);
                    if (!$codeWorkshopLink) {
                        return null;
                    }
                    $wm1 = $this->createWorkshopMeta(['workshop_id' => $wid, 'role' => '1', 'user_id' => $params['president_id']]);
                    if (!$wm1) {
                        return null;
                    }
                    $wm2 = $this->createWorkshopMeta(['workshop_id' => $wid, 'role' => '2', 'user_id' => $params['validator_id']]);
                    if (!$wm2) {
                        return NULL;
                    }
//
                    // TODO create message service
                    // right now there is only one function with
                    // low priority
                    //add first message category under new created workshop
                    MessageCategory::insert(['category_name' => 'General', 'workshop_id' => $wid, 'status' => 1]);

                    $topicParam = [
                        'topic_name'  => 'General',
                        'workshop_id' => $wid, // workshop id in which topic will belongs
                    ];
                    \Modules\Messenger\Service\TopicService::getInstance()->create($topicParam);
                    /* module specific
                                        if ($params['$code1'] == 'NSL') {
                                            //check that project template exists or not
                                            $template = $this->checkProjectTemplate();
                                            if (!empty($template))
                                                $this->createProjectAndMilestone($wid, $template, $projectEnable);
                                        }
                                        if (isset($code1) && $code1 == 'CRM') {
                                            $this->createDefaultProject('CRM', $wid, $projectEnable, 'CRM');
}
                    */
                }
                DB::connection('tenant')->commit();
                return $wid;
            } catch (\Exception $e) {
                DB::connection('tenant')->rollBack();
            }
        }

        public function getWorkshops($workshopType = "regular", $params = [])
        {
            $condition = [];
            switch ($workshopType) {
                case "qualification":
                    $condition = [
                        ['is_qualification_workshop', '!=', 0],
                    ];
                    break;
                case "event":
                    $condition = [
                        ['is_event_workshop', 1],
                    ];
                    break;
                case "regular":
                    $condition = [
                        ['is_event_workshop', 0],
                        ['is_qualification_workshop', 0],
                    ];
                    break;
                default:
            }
            $workshopData = Workshop::where($condition)->with('meta')->with(['workshop_meta' => function ($q) {
                $q->where("role", 2);
            }])->get();
            return response()->json($workshopData);
        }

        public function createWorkshopMeta($params)
        {
            $validator = Validator::make($params, [
                'workshop_id' => 'required|integer|exists:tenant.workshops,id', // do not add exists as it would give not exists in single transaction
                'user_id'     => 'required|exists:tenant.users,id',
                'role'        => 'required|integer|min:0|max:3',
                'meeting_id'  => 'nullable|integer',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            try {
                $wm = NULL;
                DB::connection('tenant')->beginTransaction();
                $wm = WorkshopMeta::create([
                    'workshop_id' => $params['workshop_id'],
                    'user_id'     => $params['user_id'],
                    'role'        => $params['role'],
                    'meeting_id'  => (isset($params['meeting_id']) ? $params['meeting_id'] : NULL),
                ]);
                //this function will check and add it to parent workshop
                $this->checkAndAddToParentCommission($params['workshop_id'], $params['user_id']);
                DB::connection('tenant')->commit();
                return $wm;
            } catch (\Exception $e) {
                DB::connection('tenant')->rollBack();
                return NULL;
            }
        }

        public function addUserToWorkshop($userId, $workshopId, $role = '0', $meetingId = NULL)
        {
            $validator = Validator::make([
                'userId'     => $userId,
                'workshopId' => $workshopId,
                'role'       => $role,
                'meeting_id' => $meetingId,
            ], [
                'workshop_id' => 'required|exists:tenant.workshops,id',
                'user_id'     => 'required|exists:tenant.users,id',
                'role'        => 'nullable|integer|min:0|max:3',
                'meeting_id'  => 'nullable|exists:meeting_id,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            // TODO check user has a permission for adding member to workshop
            $wm = WorkshopMeta::create([
                'workshop_id' => $workshopId,
                'user_id'     => $userId,
                'role'        => $role,
                'meeting_id'  => $meetingId,
            ]);
            //this function will check and add it to parent workshop
            $this->checkAndAddToParentCommission($workshopId, $userId);
            if ($wm) {
                return response()->json(['status' => TRUE, 'data' => $wm], 200);
            } else {
                return response()->json(['status' => FALSE, 'msg' => 'Something went wrong'], 500);
            }
        }

        public function getEventWorkshopsWithMemberCount($paginate, $field, $order)
        {
            $fieldable = ['workshop_name', 'code1', 'secretory', 'deputy', 'member'];
            $field = (($field && in_array($field, $fieldable)) ? $field : 'workshops.id');
            $order = (($order && ($order == 'desc' || $order == 'asc')) ? $order : 'desc');
            $data = Workshop::where('is_qualification_workshop', 3)
                ->join('users as president', 'workshops.president_id', '=', 'president.id')
                ->join('users as validator', 'workshops.validator_id', '=', 'validator.id')
                ->selectRaw(
                    "workshops.id," .
                    "workshops.workshop_name as workshop_name," .
                    "president.id as president_id," .
                    "validator.id as validator_id," .
                    "CONCAT(president.fname, ' ', president.lname) as secretory," .
                    "CONCAT(validator.fname, ' ', validator.lname) as deputy," .
                    "workshops.code1 as code1," .
                    "COUNT(DISTINCT wm.user_id) as member"
                )
                ->join('workshop_metas as wm', 'workshops.id', '=', 'wm.workshop_id')
                ->groupBy('workshop_id')
                ->orderBy($field, $order)
                ->withoutGlobalScopes();
            if ($paginate)
                return $data->paginate($paginate);
            return $data->get();
        }

        public function isWorkshopExists($workshopId)
        {
            if (Workshop::where('id', $workshopId)->withoutGlobalScopes()->count()) {
                return TRUE;
            }
            return response()->json(['status' => FALSE, 'msg' => 'Workshop Doesn\'t Exist'], 422);
        }

        public function addUserToParentWorkshop($userId, $workshopId, $role = '0', $meetingId = NULL)
        {
            $validator = Validator::make([
                'userId'     => $userId,
                'workshopId' => $workshopId,
                'role'       => $role,
                'meeting_id' => $meetingId,
            ], [
                'workshopId' => 'required|exists:tenant.workshops,id',
                'userId'     => 'required|exists:tenant.users,id',
                'role'       => 'nullable|integer|min:0|max:3',
                'meeting_id' => 'nullable|exists:tenant.meeting_id,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            //check added user should not be sec/dep of parent workshop
            $check = WorkshopMeta::where(['user_id' => $userId, 'workshop_id' => $workshopId])->whereIn('role', [1, 2])->count();
            if ($check == 0) {
                $wm = WorkshopMeta::updateOrCreate([
                    'workshop_id' => $workshopId,
                    'user_id'     => $userId,
                ], [
                    'workshop_id' => $workshopId,
                    'user_id'     => $userId,
                    'role'        => $role,
                    'meeting_id'  => $meetingId,
                ]);
            }
            if (isset($wm)) {
                return response()->json(['status' => TRUE, 'data' => $wm], 200);
            } elseif ($check >= 0) {
                return response()->json(['status' => TRUE, 'data' => []], 200);
            } else {
                return response()->json(['status' => FALSE, 'msg' => 'Something went wrong'], 500);
            }
        }

        public function checkAndAddToParentCommission($workshopId, $user_id)
        {
            $response = NULL;
            //here checking and adding member to parent commission
            $workshop_data = Workshop::withoutGlobalScopes()->with('parentWorkshop:id,workshop_type,code1,code2')->find($workshopId, ['id', 'workshop_type', 'code1']);
            if (isset($workshop_data->workshop_type) && $workshop_data->workshop_type == 2) {
                if (isset($workshop_data->parentWorkshop->id)) {
                    $response = $this->addUserToParentWorkshop($user_id, $workshop_data->parentWorkshop->id);
                }
            }
            return $response;
        }

        public function removeUserFromParentWorkshop($userId, $workshopId)
        {
            $workshop = Workshop::withoutGlobalScopes()->with('parentWorkshop:id,workshop_type,code1,code2', 'parentWorkshop.dependentWorkshop')->find($workshopId, ['id', 'workshop_type', 'code1']);
            if (isset($workshop->parentWorkshop->id)) {
                $dependent = isset($workshop->parentWorkshop->dependentWorkshop) ? $workshop->parentWorkshop->dependentWorkshop->pluck('id')->toArray() : [];
                $workshopMeta = WorkshopMeta::whereIn('workshop_id', $dependent)->where('user_id', $userId)->groupBy('role');
                if ($workshopMeta->count() == 1) {
                    WorkshopMeta::where(['workshop_id' => $workshop->parentWorkshop->id, 'user_id' => $userId])->delete();
                }
            }

        }

        public function deleteWorkshop($workshopId)
        {
            $workshop = Workshop::where('id', $workshopId)->withoutGlobalScopes()->delete();
            $meta = WorkshopMeta::where('workshop_id', $workshopId)->delete();
            $meetings = Meeting::where('workshop_id', $workshopId)->update(['status' => 0]);
            $category = MessageCategory::where('workshop_id', $workshopId)->delete();
            $topic = TopicService::getInstance()->deleteTopicsOfWorkshop($workshopId);
            return (boolean)$workshop;
        }
    }

