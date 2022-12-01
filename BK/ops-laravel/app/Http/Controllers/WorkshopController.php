<?php

    namespace App\Http\Controllers;

    use App\Http\Resources\WorkshopCollection;
    use App\Services\UserService;
    use App\Services\WorkshopService;
    use App\Topic;
    use Illuminate\Validation\Rule;
    use App\Meeting;
    use App\MessageCategory;
    use App\Presence;
    use App\Timeline;
    use App\User;
    use App\Workshop;
    use App\WorkshopCode;
    use App\WorkshopMeta;
    use Auth;
    use Carbon\Carbon;
    use DB;
    use Hash;
    use Illuminate\Http\Request;
    use Modules\Cocktail\Exceptions\InternalServerException;
    use Modules\Cocktail\Services\KctEventService;
    use Modules\Events\Exceptions\CustomException;
    use Modules\Events\Service\EventService;
    use Modules\Resilience\Services\ResilienceService;
    use phpDocumentor\Reflection\Types\Boolean;
    use phpDocumentor\Reflection\Types\Null_;
    use Validator;
    use App\Project;
    use App\Milestone;
    use function PHPSTORM_META\elementType;

    class WorkshopController extends Controller
    {

        private $core, $tenancy, $meeting, $workshopService;

        public function __construct()
        {
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);
            $this->core = app(\App\Http\Controllers\CoreController::class);
            $this->meeting = app(\App\Http\Controllers\MeetingController::class);
            $this->workshopService = WorkshopService::getInstance();
        }


        public function addWorkshop(Request $request)
        {

            if ($request->workshop_type == 1) {
                if ($this->checkUnique($request)) {
                    return response((['status' => 1, 'msg' => 'This Code already exists!']));
                }
            } else {
                $validator = Validator::make($request->all(), [
                    'code1' => ['required', Rule::notIn(['NSL', 'CRM'])],
                    'code2' => 'required',
                ]);

                $validator->after(function ($validator) use ($request) {
                    if ($this->checkValidCombination($request)) {
                        $validator->errors()->add('code2', 'This Combination already in use.Please Choose a different one!');
                    }
                });
            }

            if (isset($validator) && $validator->fails()) {
                return response((['status' => 1, 'msg' => implode(',', $validator->errors()->all())]));
            } else {
                $newRec = [];
                $workshop = [
                    'president_id'              => $request->president,
                    'validator_id'              => json_decode($request->validator)[0],
                    'workshop_name'             => $request->workshop_name,
                    'workshop_desc'             => $request->workshop_desc,
                    'workshop_type'             => $request->workshop_type,
                    'is_private'                => $request->is_private,
                    'is_dependent'              => isset($request->is_dependent) ? $request->is_dependent : 0,
                    'is_qualification_workshop' => isset($request->is_qualification_workshop) ? $request->is_qualification_workshop : 0,
                ];

                $query = Workshop::where('workshop_name', $request->workshop_name);
                if ($request->workshop_type == 1) {
                    $query->where('code1', $request->code1);
                    $workshop['code1'] = $request->code1;
                } else {
                    $query->where('code2', $request->code2);
                    $workshop['code1'] = $request->code1;
                    $workshop['code2'] = $request->code2;
                }
                $row_count = $query->count();

                if ($row_count == 0) {

                    DB::connection('tenant')->beginTransaction();
                    $codeId = WorkshopCode::insertGetId(['code' => $request->code1]);
                    $id = Workshop::insertGetId($workshop);
                    if ($id) {
                        WorkshopCode::where('id', $codeId)->update(['workshop_id' => $id]);
                        $workshop_meta[0] = ['workshop_id' => $id, 'role' => '1', 'user_id' => $request->president];
                        foreach (json_decode($request->validator) as $k => $item) {
                            $workshop_meta[($k + 1)] = ['workshop_id' => $id, 'role' => '2', 'user_id' => $item];
                        }
                        $newRec = WorkshopMeta::insert($workshop_meta);
                        //here checking and adding member to parent commission
                        //this function will check and add it to parent workshop
                        foreach ($workshop_meta as $item) {
                            $response = $this->workshopService->checkAndAddToParentCommission($item['workshop_id'], $item['user_id']);
                            if (empty($response)) {
                                //checking if response is null and member Not needed to add in any parent default member add case
                                DB::connection('tenant')->commit();
                            } else {
                                $response = (json_decode($response->getContent()));
                            }
                            //checking if response is true and member added to parent
                            if (isset($response->status) && $response->status) {
                                DB::connection('tenant')->commit();
                            } elseif (isset($response->status) && !$response->status) {
                                //checking if response is false and member Not added to parent
                                DB::connection('tenant')->rollBack();
                            }
                        }

                        //add first message category under new created workshop
                        MessageCategory::insert(['category_name' => 'General', 'workshop_id' => $id, 'status' => 1]);
                        // add default entry in project table

                        $project = Project::create([
                            'project_label'      => 'Projet Bazar',
                            'user_id'            => Auth::user()->id,
                            'wid'                => $id,
                            'color_id'           => 1,
                            'is_default_project' => 1,
                            'end_date'           => '2099-12-31 00:00:00',
                        ]);

                        //add default Entry in milestone Table
                        Milestone::create([
                            'project_id'           => $project->id,
                            'label'                => 'Étape Bazar',
                            'user_id'              => Auth::user()->id,
                            'end_date'             => '2099-12-31 00:00:00',
                            'color_id'             => 1,
                            'start_date'           => Carbon::now(),
                            'is_default_milestone' => 1,
                        ]);
                        // dd($project);
                        if (!$newRec && !$project) {
                            DB::connection('tenant')->rollBack();
                        }
                    } else {
                        DB::connection('tenant')->rollBack();
                    }
                    DB::connection('tenant')->commit();
                }
                return response()->json(['new_rec' => $newRec, 'row_count' => $row_count, 'all_data' => $this->core->getWorkshopByLogin()]);
            }
        }


        public static function checkValidCombination($data, $id = NULL)
        {
            if (!empty($id)) {
                $query = Workshop::where(['code1' => isset($data->code1) ? $data->code1 : $data['code1'], 'code2' => isset($data->code2) ? $data->code2 : $data['code2'], 'workshop_type' => 2])->where('id', '!=', $id)->withoutGlobalScopes()->count();
            } else
                $query = Workshop::where(['code1' => isset($data->code1) ? $data->code1 : $data['code1'], 'code2' => isset($data->code2) ? $data->code2 : $data['code2'], 'workshop_type' => 2])->count();

            if ($query > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        }

        public static function checkUnique($data, $id = NULL)
        {
            if (!empty($id)) {
                $query = Workshop::where(['code1' => isset($data->code1) ? $data->code1 : $data['code1'], 'workshop_type' => 1])->where('id', '!=', $id)->withoutGlobalScopes()->count();
            } else
                $query = Workshop::where(['code1' => isset($data->code1) ? $data->code1 : $data['code1'], 'workshop_type' => 1])->withoutGlobalScopes()->count();

            if ($query > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        }

        public function updateWorkshop(Request $request)
        {
            $updateParent = [];
            $workshop = [
                'president_id'              => $request->president,
//            'validator_id' => $request->validator,
                'workshop_name'             => $request->workshop_name,
                'is_dependent'              => isset($request->is_dependent) ? $request->is_dependent : 0,
                'workshop_desc'             => $request->workshop_desc,
                //'workshop_type'             => $request->workshop_type,
                'is_private'                => $request->is_private,
                'is_qualification_workshop' => isset($request->is_qualification_workshop) ? $request->is_qualification_workshop : 0,
            ];

            $row_count = 0;
            if ($row_count == 0) {
//            DB::connection('tenant')->beginTransaction();

                $updateParent = Workshop::where('id', $request->id)->withoutGlobalScopes()->update($workshop);
                if ($updateParent) {
                    /*if (!*/
                    WorkshopMeta::where('workshop_id', $request->id)->where('role', 1)->update(['user_id' => $request->president]);
                    /*)*/
//                    DB::connection('tenant')->rollBack();
                    $row_count = 1;

                    $newValidator = array_unique(json_decode($request->validator));
                    if (count($newValidator) > 0) {
                        WorkshopMeta::where('workshop_id', $request->id)->where('role', 2)->delete();
                    }
                    $workshopM = WorkshopMeta::where('workshop_id', $request->id)->get(['id', 'user_id', 'role']);
                    foreach ($newValidator as $k => $item) {
                        $isExist = $workshopM->where('user_id', $item)->first();
                        if (isset($isExist->id)) {
                            if ($isExist->role == 1) {
                                WorkshopMeta::create(['workshop_id' => $request->id, 'role' => '2', 'user_id' => $item]);
                            } else {
                                WorkshopMeta::where('user_id', $item)->where('workshop_id', $request->id)->update(['role' => 2]);
                            }
                        } else {

                            WorkshopMeta::create(['workshop_id' => $request->id, 'role' => '2', 'user_id' => $item]);
                        }

                    }

                } else {
                    DB::connection('tenant')->rollBack();
                }
                DB::connection('tenant')->commit();
            }
            return response()->json([
                'status' => TRUE,
                'data'   => ['new_rec' => $updateParent, 'row_count' => $row_count],
            ]);
        }


        public
        function getWorkshopById($id)
        {
            $data = Workshop::with(['meta' => function ($q) {
                $q->orderBy(DB::raw("FIELD(role,'1','2','0','3')"));
            }, 'workshop_meta'             => function ($q) {
                $q->where("role", 2);
            }])->where('id', $id)->withoutGlobalScopes()->first();

            return response()->json($data);
        }

        public
        function getWorkshops(Request $request)
        {
            $data = Workshop::with('meta')->with(['workshop_meta' => function ($q) {
                $q->where("role", 2);
            }])->get();
            return response()->json($data);
        }

        /*
         * this function is to send all workshops of logged In user
         * its a copy of above function as above one used many places
         * so we made changes in below one
         * */
        public function getUserProfileWorkshops(){
            $data = Workshop::whereHas('meta', function ($q) {
                $q->where('user_id', Auth::user()->id);
            })->with('meta')->with(['workshop_meta' => function ($q) {
                $q->where("role", 2);
            }])->get();
            return response()->json($data);
        }

        public
        function getQualificationWorkshops(Request $request)
        {
            $data = Workshop::whereIn('is_qualification_workshop', [1, 2])->withoutGlobalScopes()->with(['workshop_meta' => function ($q) {
                // $q->where("role", 2);
            }, 'meta'])->get(['id', 'is_qualification_workshop', 'workshop_name', 'code1', 'is_private', 'president_id', 'validator_id']);


            return response()->json(['status' => TRUE, 'data' => $data], 200);
        }

//    public function getUserWorkshops(Request $request) {
//        $workshopData = [];
//        if (Auth::user()->role == 'M1' || Auth::user()->role == 'M0') {
//            $workshopMeta = Workshop::orderBy('id', 'DESC')->get();
//        } else {
//            $workshopMeta_ids = WorkshopMeta::where('user_id', Auth::user()->id)->pluck('workshop_id', 'id');
//            $workshopMeta = Workshop::whereIn('id', $workshopMeta_ids)->orderBy('id', 'DESC')->get();
//        }
//        foreach ($workshopMeta as $value) {
//            $user_records = WorkshopMeta::where('workshop_id', $value->id)->groupBy('user_id','role')->where('role', '!=', '3')->get()->toArray();
//            $meta = WorkshopMeta::where('workshop_id', $value->id)->groupBy('user_id')->where('role', '!=', '3')->get()->toArray();
//            $value['count'] = count($meta);
//            $value['meta'] = $user_records;
//
//            $workshopData[] = $value;
//        }
//        $data['workshop']=$workshopData;
//        $data['store_workshop']=$this->core->getWorkshopByLogin();
//        return response()->json($data);
//    }

        public
        function getWorkshopList($id = NULL)
        {
            try {
                if ((Auth::user()->role == 'M1' || Auth::user()->role == 'M0') && $id == NULL) {
                    $data = Workshop::get(['id', 'workshop_name']);
                } else {
                    $userId = ($id != NULL) ? $id : Auth::user()->id;
                    $data = Workshop::whereIn('id', function ($query) use ($userId) {
                        $query->select('workshop_id')
                            ->from('workshop_metas')->where('user_id', '=', $userId);
                    })->get(['id', 'workshop_name']);
                }
                return response()->json(['status' => TRUE, 'data' => $data], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        public
        function getUserWorkshops(Request $request)
        {

            $workshopData = [];
            if (in_array(Auth::user()->role, ['M1', 'M0'])) {

                $workshopData = Workshop::withCount(['workshop_meta' => function ($q) {
                    $q->where('role', '<', '3')->whereIn('user_id', function ($query) {
                        $query->select('id')
                            ->from('users')
                            ->whereRaw('users.id = workshop_metas.user_id');
                    });
                }])->whereNull('is_qualification_workshop')->OrWhere('is_qualification_workshop', 0)->with(['workshop_meta' => function ($q) {
                    $q->where("role", 2);
                }, 'president:id,fname,lname'])->orderBy('id', 'DESC')->get([\DB::raw("CASE WHEN setting IS NULL THEN '{\"custom_graphics_enable\":0}' ELSE (select setting) END as setting"), 'id', 'code1', 'code2', 'president_id', 'validator_id', 'workshop_name', 'setting']);
            } else {
                $workshopMeta_ids = WorkshopMeta::where('user_id', Auth::user()->id)->get(['workshop_id', 'id'])->pluck('workshop_id', 'id');
                if (count($workshopMeta_ids) > 0) {
                    $workshopData = Workshop::whereIn('id', $workshopMeta_ids)
                    ->where(function ($q) {
                        $q->whereNull('is_qualification_workshop');
                        $q->OrWhere('is_qualification_workshop', 0);
                    })
                    ->withCount(['workshop_meta' => function ($q) {
                        $q->where('role', '<', '3');
                    }])->with(['workshop_meta' => function ($q) {
                        $q->where("role", 2);
                    }, 'president:id,fname,lname'])->orderBy('id', 'DESC')->orderBy('id', 'DESC')->get([\DB::raw("CASE WHEN setting IS NULL THEN '{\"custom_graphics_enable\":0}' ELSE (select setting) END as setting"), 'id', 'code1', 'code2', 'president_id', 'validator_id', 'workshop_name', 'setting']);
                } else {
                    $workshopData = [];
                }
            }
            foreach ($workshopData as $k => $value) {

                $meta = WorkshopMeta::where('workshop_id', $value->id)->groupBy('user_id')->where('role', '<', '3')->whereExists(function ($query) {
                    $query->select('id')
                        ->from('users')
                        ->whereRaw('users.id = workshop_metas.user_id');
                })->get()->toArray();

                $value[$value->id] = count($meta);
                $workshopData[$k]->workshop_meta_count = count($meta);
            }

            $data['workshop'] = $workshopData;
            $data['store_workshop'] = $this->core->getWorkshopByLogin();
            return response()->json($data);
        }


        public
        function getWorkshopsForDocs()
        {

            if (Auth::user()->role == 'M1') {
                return DB::connection('tenant')->select(DB::raw("select *, w.id from workshops w left join workshop_metas wm ON wm.workshop_id = w.id OR w.is_private = 0 where w.is_qualification_workshop=0  group by w.id order by w.id desc "));
            } else {
                return DB::connection('tenant')->select(DB::raw("select *,w.id from workshops w left join workshop_metas wm ON wm.workshop_id = w.id where wm.user_id = '" . Auth::user()->id . "' OR w.is_private = 0 and w.is_qualification_workshop=0  group by w.id order by w.id desc"));
            }
            return response()->json($data);
        }

        public
        function getPresident(Request $request)
        {
            return response()->json($this->presidentList());
        }

        public
        function deleteWorkshop($id)
        {
            $res = 0;
            if (config('constants.NEWSLETTER') == 1) {
                $workshop = Workshop::where('id', $id)->first();
                if ($workshop->code1 == "NSL") {
                    $res = 0;
                    // return response()->json($res);
                } else {
                    $workshop->delete();
                    $res = 1;
                }
            } else {
                if (Workshop::where('id', $id)->delete())
                    $res = 1;
            }
            return response()->json($res);
        }

        public
        function getWorkshopData()
        {

            $data['users'] = $this->presidentList();
            $data['codeList'] = WorkshopCode::groupBy('code')->get();
            return response()->json($data);
        }

        public
        function getWorkshopMembers(Request $request)
        {

            $data = $this->getWorkshopMemberArray($request);
            return response()->json($data);
        }

        public
        function updateMemberStatus(Request $request)
        {
            try {
            DB::connection('tenant')->beginTransaction();
            
            //to check user is eligible to be a sec/dep
            if ($request->status == 2 || $request->status == 1) {
                $user = UserService::getUser(['id', 'role_commision','role'], $request->id);
                if ($user->role_commision == 0 && $user->role=='M2') {
                    return response()->json(['status' => FALSE, 'msg' => __('message.member_not_allowed_dep')]);
                }
            }

            if ($request->status == 2) {
                //this code we are using to remove role of user
                //as sec can be sec only when we have another dep
                //sec/dep if no deputy in list
                $this->removeWorkshopRole((int)$request->wid, (int)1, (int)$request->id);
                //condition to remove dep if sec have the role
                $metaSecDep = WorkshopMeta::where('workshop_id', $request->wid)->whereIn('role', [1, 2])->get(['id', 'user_id', 'role']);
                $sec = collect($metaSecDep)->where('role', 1)->first();
                if (isset($sec->user_id)) {
                    $sameUserDep = collect($metaSecDep)->where('user_id', $sec->user_id)->where('role', 2)->count();
                    if ($sameUserDep > 0) {
                        $this->removeWorkshopRole((int)$request->wid, (int)2, (int)$sec->user_id);
                    }
                }

                $checkRole = WorkshopMeta::where('workshop_id', $request->wid)->where('user_id', $request->id)->first(['id', 'user_id', 'role']);

                if ((isset($checkRole->id) && $checkRole->role == 1)) {
                    WorkshopMeta::insert(['user_id' => $request->id, 'workshop_id' => $request->wid, 'role' => 2]);
                }
            }
            if ($request->status == 1) {
                //this code we are using to remove role of user
                //as sec can be sec only when we have another dep
                //sec/dep if no deputy in list
                $this->removeWorkshopRole((int)$request->wid, (int)2, (int)$request->id);
                //taking sec
                $sec = WorkshopMeta::where('workshop_id', $request->wid)->where('role', 1)->first();
               //taking dep
                $dep = WorkshopMeta::where('workshop_id', $request->wid)->where('role', 2)->count();
               //checking if sec have and no dep,then add him as dep also
                if (isset($sec->id) && ($dep == 0)) {
                    WorkshopMeta::updateOrCreate(['workshop_id' => $request->wid, 'user_id' => $request->id, 'role' => 2], ['workshop_id' => $request->wid, 'user_id' => $request->id, 'role' => 2]);
                }
                WorkshopMeta::where('workshop_id', $request->wid)->where('role', 1)->update(['role' => 0]);
                $status = WorkshopMeta::where('workshop_id', $request->wid)->where('role', 1)->first();
                if ($status == NULL || $status == '') {
                    WorkshopMeta::insert(['user_id' => $request->id, 'workshop_id' => $request->wid, 'role' => 1]);
                }
            }

            $chk_update = WorkshopMeta::where('user_id', $request->id)->where('workshop_id', $request->wid)->where('role', $request->status)->first();
            if ($chk_update == NULL || $chk_update == '') {
                WorkshopMeta::where('user_id', $request->id)->where('workshop_id', $request->wid)->update(['role' => $request->status]);
            }

            if (isset($request->is_last) && ($request->is_last && $request->status == 0)) {
                $this->lastDepRemove($request);
            }

            $wmData = WorkshopMeta::selectRaw('count(*) as user_count,user_id,id')
                ->where('workshop_id', $request->wid)
                ->groupBy('user_id')->get();
            if ($wmData->count() > 0) {
                foreach ($wmData as $val) {
                    if ($val->user_count > 1) {
                        WorkshopMeta::where('user_id', $val->user_id)->where('workshop_id', $request->wid)->where('role', 0)->delete();
                    }
                }
            }
            EventService::getInstance()->changeEventOrganiser($request->wid, $request->id, $request->status);
            $data = $this->getWorkshopMemberArray($request);
            DB::connection('tenant')->commit();
            return response()->json($data);
            } catch (CustomException $e) {
                DB::connection('tenant')->rollback();
                return $e->render();
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
            }
        }


        public
        function getWorkshopMemberArray($request)
        {

            $data = [];
            $wMeta = WorkshopMeta::with(['user', 'user.entity' => function ($q) {
                $q->select('entities.id', 'entities.long_name', 'entities.entity_type_id');
            }])->where('workshop_id', $request->wid)->where('role', '!=', '3')->where('role', '!=', '4')->whereExists(function ($query) {
                $query->select('id')
                    ->from('users')
                    ->whereRaw('users.id = workshop_metas.user_id');
            })->groupBy('role', 'user_id')->orderBy('user_id', 'ASC')->get();
            $count = 0;
            foreach ($wMeta as $val) {
                $company = NULL;
                $union = NULL;
                $unionPos = NULL;
                $companyPos = NULL;
                if ($val->user->entity->isNotEmpty()) {
                    collect($val->user->entity)->each(function ($v) use (&$company, &$union, &$unionPos, &$companyPos, &$val) {
                        if ($v->entity_type_id == 2) {
                            $company = $v->long_name;
                            $companyPos = isset($v->pivot->entity_label) ? $v->pivot->entity_label : '';
                        } elseif ($v->entity_type_id == 3) {
                            $_union = collect($val->user->entity)->where('entity_type_id', 3)->first();
                            $union = (!empty($_union)) ? $_union->long_name : '';
                            $unionPos = isset($_union->pivot->entity_label) ? $_union->pivot->entity_label : '';
                        }
                    });
                }
                if (isset($data[$val->user_id])) {
                    $count++;
                    if (in_array(2, $roles) || in_array(1, $roles)) {
                        if ($val->role !== 0) {
                            $roles[] = $val->role;
                        }
                    } else {
                        if (($key = array_search(0, $roles)) !== FALSE) {
                            if ($val->role !== 0) {
                                $roles[$key] = $val->role;
                            }
                            //unset($roles[$key]);
                        }
                    }
                    $data[$val->user_id]['member'] = ['id'               => $val->user->id, 'role_commision' => $val->user->role_commision, 'main_role' => $val->user->role, 'role' => ($roles), 'email' => $val->user->email, 'lname' => $val->user->lname, 'fname' => $val->user->fname, 'member_name' => $val->user->lname . ' ' . $val->user->fname, 'union' => isset($val->user->union[0]) ? $val->user->union[0]->union_code : '', 'society' => $company, 'user_id' => $val->user->id, 'count' => $count,
//                                                      'entity' => ($val->user->entity->isNotEmpty()) ? $val->user->entity[0]->long_name : '',
                                                      'entity'           => $company,
                                                      'union'            => $union,
                                                      'company_position' => $companyPos,
                                                      'union_position'   => $unionPos,
                    ];
                } else {
                    $roles = [];
                    $count = 1;
                    $roles[] = $val->role;
                    $data[$val->user_id] = ['member' => ['id'               => $val->user->id, 'role_commision' => $val->user->role_commision, 'main_role' => $val->user->role, 'role' => ($roles), 'email' => $val->user->email, 'lname' => $val->user->lname, 'fname' => $val->user->fname, 'member_name' => $val->user->lname . ' ' . $val->user->fname, 'union' => isset($val->user->union[0]) ? $val->user->union[0]->union_code : '', 'society' => $company, 'user_id' => $val->user->id, 'count' => $count, 'entity' => $company,
                                                         'union'            => $union,
                                                         'company_position' => $companyPos,
                                                         'union_position'   => $unionPos,
                    ],
                    ];
                }
                //dump($val->user);

            }
//reset array index value
            $data = array_values($data);
//set array order by member name
            usort($data, function ($a, $b) {
                return $a['member']['lname'] <=> $b['member']['lname'];
                //return $a['member']['member_name'] <=> $b['member']['member_name'];
            });
            //filter validator
            $workshop = [];
            $president = [];
            $validator = [];
            foreach ($data as $k => $val) {
                if (isset($val['member']['role']) && count($val['member']['role']) == 2) {
                    $workshop['president_id'] = $val['member']['user_id'];
                    $workshop['validator_id'] = $val['member']['user_id'];
                    break;
                } else {
                    if (in_array(2, $val['member']['role'])) {
                        $workshop['validator_id'] = $val['member']['user_id'];
                        $validator[] = $val;
                        unset($data[$k]);
                    }
                    if (in_array(1, $val['member']['role'])) {
                        $workshop['president_id'] = $val['member']['user_id'];
                        $president[] = $val;
                        unset($data[$k]);
                    }
                }
            }
// //filter validator
//         $validator = [];
//         foreach ($data as $k => $val) {
//             if (in_array(2, $val['member']['role'])) {
//                 $workshop['validator_id'] = $val['member']['user_id'];
//                 $validator[] = $val;
//                 unset($data[$k]);
//             }
//         }
// //filter president
//         $president = [];
//         foreach ($data as $k => $val) {
//             if (in_array(1, $val['member']['role'])) {
//                 $workshop['president_id'] = $val['member']['user_id'];
//                 $president[] = $val;
//                 unset($data[$k]);
//             }
//         }
//update workshop table in president and validator
            if (!empty($workshop)) {
                Workshop::where('id', $request->wid)->update($workshop);
            }

            return array_merge($president, $validator, $data);
        }

        public
        function addMember(Request $request)
        {
            DB::connection('tenant')->beginTransaction();
            $newmember = [];
            $emails = [];
            //$mail_check = User::where('email', strtolower($request->email))->where('role', 'M3')->first();
            $mail_check = User::where('email', strtolower($request->email))->first();

//        if (is_array($mail_check) && count($mail_check) > 0) {
            if (isset($mail_check->role) && ($mail_check->role == 'M3' || $mail_check->sub_role == 'C1')) {
                $newmember = ['fname' => $mail_check->fname, 'lname' => $mail_check->lname, 'email' => $mail_check->email];
                $emails[] = $mail_check->emaill;
                $user_id=$mail_check->id;
                $res = User::where('email', strtolower($request->email))->update(['password' => Hash::make(strtolower($request->email)), 'role' => 'M2', 'login_count' => '0','fname'=>$request->firstname,'lname'=>$request->lastname]);
//                if ($res > 0) {
//                    return response()->json(['status' => 1]);
//                } else {
//                    return response()->json(['status' => 0]);
//                }
            }
            else {
                if ($request->data != NULL) {
                    $userData = json_decode($request->data);
                    $user_id = $userData->id;
                    $emails[] = $userData->text;
                    $fnameLname = explode(' ', $userData->value);
                    $newmember = ['fname' => $fnameLname[0], 'lname' => $fnameLname[1], 'email' => $userData->text];

                }
                else {

                    if (!isset($mail_check->role)) {
                        $hostname = $this->getHostNameData();
//                $hostCode = generateRandomValue(3);
                        $hostCode = DB::connection('mysql')->table('hostname_codes')->where(['fqdn' => $hostname->fqdn])->first(['hash']);
                        $randCode = generateRandomValue(3);
//                $newCode = setPasscode($hostCode, $randCode);
                        $newCode = setPasscode($hostCode->hash, $randCode);

                        $userArray = [
                            'fname'      => $request->firstname,
                            'lname'      => $request->lastname,
                            'email'      => strtolower($request->email),
                            'password'   => Hash::make(strtolower($request->email)),
                            'role'       => 'M2',
                            'login_code' => $newCode['userCode'],
                            'hash_code'  => $newCode['hashCode'],
                        ];

                        $userData = User::create($userArray);
                        $user_id = $userData->id;
                        $emails[] = $userData->email;

                        $newmember = ['fname' => $userData->fname, 'lname' => $userData->lname, 'email' => $userData->email];
                        //this code is added for event
                        if (!isset($request->email_send)) {
                            //start user domain invite email
                            $route = route(
                                'redirect-meeting-view', [
                                    'userid' => base64_encode($user_id),
                                    'type'   => 'm',
                                    'url'    => str_rot13('dashboard'),
                                ]
                            );
                            $_workshop_data = Workshop::withoutGlobalScopes()->with('meta')->find($request->workshop_id);
                            $dataMail = $this->meeting->getUserMailData('user_email_setting',$_workshop_data);
                            $subject = utf8_encode($dataMail['subject']);

                            $mailData['mail'] = ['subject' => $subject, 'email' => $userArray['email'], 'password' => $userArray['password'], 'url' => $route,'workshop_data' => $_workshop_data];
                            $this->core->SendEmail($mailData, 'new_user');
                        }
                        //end user domain invite email
                    } else {
                        $newmember = ['fname' => $mail_check->fname, 'lname' => $mail_check->lname, 'email' => $mail_check->email];
                        $user_id = $mail_check->id;
                        $emails[] = $mail_check->email;

                    }

                }
            }
            $existMember = WorkshopMeta::where('workshop_id', $request->workshop_id)->where('user_id', $user_id)->first();
            if (isset($existMember->role) && $existMember->role!='3') {
                $status = 0;
            } else {
                $status = 1;
                $userMeta = WorkshopMeta::updateOrCreate(
                    ['workshop_id' => $request->workshop_id, 'user_id' => $user_id], ['workshop_id' => $request->workshop_id, 'user_id' => $user_id, 'role' => 0,'meeting_id'=>NULL]);
                $workshop_data = Workshop::withoutGlobalScopes()->with('meta')->find($request->workshop_id);
                //this code is added for event

                if (!isset($request->email_send)) {
                    $dataMail = $this->getMailData($workshop_data, 'commission_new_user');
                    $subject = $dataMail['subject'];
                    $route_members = $dataMail['route_members'];
                    $this->alertNewMember($workshop_data, $newmember);
                    $mailData['mail'] = ['subject' => ($subject), 'emails' => $emails, 'workshop_data' => $workshop_data, 'url' => $route_members];
                    $this->core->SendMassEmail($mailData, 'new_commission_user');
                }
                //getting all next meeting from current time
                $meetings = Meeting::with(['doodleDates' => function ($query) {
                    $query->whereDate('date', '>', Carbon::now('Europe/Paris')->format('Y-m-d'))->orderBy('date', 'asc');
                }])->where('workshop_id', $request->workshop_id)->where(function ($q) {
                    $q->where(DB::raw('concat(date," ",start_time)'), '>=', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'))->orWhere('meeting_date_type', 0);
                })->orderBy('date', 'desc')->get(['id', 'meeting_date_type']);
                //adding user presense entry for that meeting

                foreach ($meetings as $vals) {
                    if ($vals->meeting_date_type == 1) {
                        $presenceData[] = [
                            'workshop_id'     => $request->workshop_id,
                            'meeting_id'      => $vals->id,
                            'user_id'         => $user_id,
                            'register_status' => 'NI',
                            'presence_status' => 'ANE',
                        ];
                    } elseif ($vals->meeting_date_type == 0 && count($vals->doodleDates) > 0) {
                        $presenceData[] = [
                            'workshop_id'     => $request->workshop_id,
                            'meeting_id'      => $vals->id,
                            'user_id'         => $user_id,
                            'register_status' => 'NI',
                            'presence_status' => 'ANE',
                        ];
                    }
                }

                // insert presense data
                if (!empty($presenceData)) {
                    Presence::insert($presenceData);
                }
            }

            if ($status == 1 /*&& (!isset($request->email_send))*/) {
//                    EventService::getInstance()->sendRegisterEmail($request);
                    ResilienceService::getInstance()->sendRegisterEmail($request);
                    //here checking and adding member to parent commission
                    //this function will check and add it to parent workshop
                    $response = $this->workshopService->checkAndAddToParentCommission($request->workshop_id, $user_id);
                    if (empty($response)) {
                        //checking if response is null and member Not needed to add in any parent default member add case
                        DB::connection('tenant')->commit();
                    } else {
                        $response = (json_decode($response->getContent()));
                    }
                    //checking if response is true and member added to parent
                    if (isset($response->status) && $response->status) {
                        DB::connection('tenant')->commit();
                    } elseif (isset($response->status) && !$response->status) {
                        //checking if response is false and member Not added to parent
                        DB::connection('tenant')->rollBack();
                        $status = 0;
                    }
                } else {
                    DB::connection('tenant')->rollBack();
                    $status = 0;
                }
                if ($request->has('event_uuid')) {
                    return $user_id;
            }
            return response()->json(['status' => $status]);
        }


        public
        function getHostNameData()
        {
            $this->tenancy->website();
            $hostdata = $this->tenancy->hostname();
            $domain = @explode('.' . env('HOST_SUFFIX'), $hostdata->fqdn)[0];
            //$domain = config('constants.HOST_SUFFIX');
            session('hostdata', ['subdomain' => $domain]);
            return $this->tenancy->hostname();
        }

        public
        function getNonWorkshopUsers(Request $request)
        {
            $data = User::select(DB::raw("CONCAT(lname,' ',fname) as text"), 'id')->get();
            return response()->json($data);
        }

        public
        function getTimeline(Request $request)
        {
            $data = Timeline::with('user')->where('workshop_id', $request->wid)->get();
            return response()->json($data);
        }

        public
        function deleteMember(Request $request)
        {
            try {
            $this->workshopService->removeUserFromParentWorkshop($request['user_id'], $request['wid']);
            $res = 0;
            if (WorkshopMeta::where('user_id', $request['user_id'])->where('workshop_id', $request['wid'])->delete()) {
                $res = 1;
                $this->lastDepRemove($request);
                    KctEventService::getInstance()->removeWorkshopMember($request['wid'], $request['user_id']);
               //removing all presence entries for member when we remove him from workshop
                Presence::where('user_id', $request['user_id'])->where('workshop_id', $request['wid'])->delete();
            }

            $data = $this->getWorkshopMemberArray($request);
            return response()->json($data);
            } catch (InternalServerException $e) {
                return response()->json(['status' => false, 'msg' => 'Internal Server Error'], 200);
            }
        }

        public
        function getFilterdWorkshop($val)
        {
            $data = Workshop::where('workshop_name', 'like', '%' . $val . '%')->get();
            return response()->json($data);
        }

        public
        function checkWorkShopAdmin()
        {
            $res = WorkshopMeta::where(['user_id' => Auth::user()->id, 'role' => 1])->count();
            return response()->json($res);
        }

//Added by vijay for get admin workshop
        public
        function getAdminWorkshop()
        {
            $res = WorkshopMeta::with('workshop')->where(['user_id' => Auth::user()->id, 'role' => 1])->get();
            return response()->json($res);
        }

        public
        function presidentList()
        {
            return User::select(DB::raw("CONCAT(fname,' ',lname) as text"), 'id', 'role_commision')->get();
        }

        public
        function getDashboardWorkshop()
        {
            $data['workshops'] = Workshop::with('meetings')->get();
            return response()->json($data);
        }

        function getMailData($workshop_data, $key)
        {
            $currUserFname = Auth::user()->fname;
            $currUserLname = Auth::user()->lname;
            $currUserEmail = Auth::user()->email;
            $settings = getSettingData($key);
            $orgDetail = getOrgDetail();
            $member = workshopValidatorPresident($workshop_data);
            $keywords = [
                '[[UserFirstName]]', '[[UserLastName]]', '[[UserEmail]]', '[[WorkshopLongName]]', '[[WorkshopShortName]]', '[[WorkshopPresidentFullName]]',
                '[[WorkshopvalidatorFullName]]',
                '[[ValidatorEmail]]', '[[PresidentEmail]]', '[[PresidentPhone]]', '[[OrgName]]',
                '[[OrgShortName]]',
            ];
            $values = [
                $currUserFname, $currUserLname, $currUserEmail, $workshop_data->workshop_name, $workshop_data->code1,$member['p']['fullname'], $member['v']['fullname'],
                 $member['v']['email'], $member['p']['email'], $member['p']['phone'], $orgDetail->name_org, $orgDetail->acronym,
            ];

            $subject = (str_replace($keywords, $values, $settings->email_subject));

            $route_members = route('redirect-app-url', ['url' => str_rot13('organiser/commissions/' . $workshop_data->id . '/members')]);
            return ['subject' => $subject, 'route_members' => $route_members];
        }


//@todo
        public
        function getDiligence($id)
        {
            $data = WorkshopMeta::where('workshop_id', $id)->where('role', '!=', 3)->groupBy('user_id')->get();
            for ($i = 1; $i <= 12; $i++) {
                $months = date("Y-m-d", strtotime(date('Y-m-01') . " -$i months"));
            }
            $datas = [];
            $meeting = Meeting::where('workshop_id', $id)->whereBetween('date', [$months, Carbon::now()->format('Y-m-d')])->get(['id', 'name', 'workshop_id']);
            $meetingPresence = Presence::whereIn('meeting_id', $meeting->pluck('id'))->orderBy('id', 'asc')->get();
            $meetingUser = [];
            foreach ($meetingPresence as $item) {
                $meetingUser[$item->user_id][$item->meeting_id] = $item;
            }
            foreach ($data as $datum) {
                $key = array_key_exists($datum->user_id, $meetingUser);

                if (($key !== FALSE)) {
                    foreach ($meetingUser[$datum->user_id] as $meet) {
                        if (isset($datas[$datum->user_id])) {
                            if ($meet->presence_status == 'P' && ($meet->user_id == $datum->user_id)) {
                                $datas[$datum->user_id]['presence'] = ($datas[$datum->user_id]['presence'] + 1);
//                            $datas[$datum->user_id][$meet->meeting_id] = $meet;
                            } else {
                                $datas[$datum->user_id]['not'] = ($datas[$datum->user_id]['not'] + 1);
//                            $datas[$datum->user_id][$meet->meeting_id] = $meet;
                            }

                        } else {
                            if ($meet->presence_status == 'P' && ($meet->user_id == $datum->user_id)) {
                                $datas[$datum->user_id]['presence'] = (1);
                                $datas[$datum->user_id]['not'] = (0);
//                            $datas[$datum->user_id][$meet->meeting_id] = $meet;
                            } else {
                                $datas[$datum->user_id]['presence'] = (0);
                                $datas[$datum->user_id]['not'] = (1);
                            }
//                        if($datas[1])
//                        dump($datas[$datum->user_id],$meet,'first');
                        }
                    }
                    $datas[$datum->user_id]['total'] = count($meetingUser[$datum->user_id]);

                }

            }
            return response()->json(['status' => TRUE, 'data' => $datas], 200);
            //return response()->json($datas);
        }

        public
        function alertNewMember($workshop_data, $newMember)
        {

            $this->tenancy->website();
            $hostname = $this->tenancy->hostname();
            $acc_id = $hostname->id;
            // $acc_id = 1;
            $super_permission = DB::connection('mysql')->table('account_settings')->where('account_id', $acc_id)->first();
            if ($super_permission->new_member_alert == 1) {
                $data = User::where('role', 'M1')->where('alert_new_member', TRUE)->get(['id', 'email', 'role', 'fname', 'lname', 'alert_new_member']);
                if ($data->count() > 0) {
                    //this is commented as we dont have template yet
//                if (session()->has('lang') && session()->get('lang') == "FR") {
                    $settings = getSettingData('alert_new_member_email');
//                }else{
//                    $settings = getSettingData('alert_new_member_email_EN');
//                }

                    $member = workshopValidatorPresident($workshop_data);
                    $keywords = [
                        '[[UserFirsrName]]', '[[UserLastName]]', '[[UserEmail]]', '[[WorkshopLongName]]', '[[WorkshopShortName]]', '[[WorkshopPresidentFullName]]',
                        '[[WorkshopvalidatorFullName]]', '[[ValidatorEmail]]', '[[PresidentEmail]]',
                    ];
                    $values = [
                        $newMember['fname'], $newMember['lname'], $newMember['email'], $workshop_data->workshop_name, $workshop_data->code1, '', '', $member['v']['email'], $member['p']['email'],
                    ];
                    $subject = (str_replace($keywords, $values, $settings->email_subject));
                    $route_members = route('redirect-app-url', ['url' => str_rot13('organiser/commissions/' . $workshop_data->id . '/members')]);

//                 $dataMail = $this->getMailData($workshop_data, 'alert_new_member_email');
                    $emails = [];
                    foreach ($data as $user) {
                        $emails[] = $user->email;
                    }
                    $mailData['mail'] = ['subject' => ($subject), 'emails' => $emails, 'workshop_data' => $workshop_data, 'user_fname' => $newMember['fname'], 'user_lname' => $newMember['lname'], 'user_email' => $newMember['email'], 'url' => $route_members];
                    $this->core->SendMassEmail($mailData, 'alert_new_member');
                }
            }
        }

        public
        function searchWorkshop($searchkeyword)
        {
            try {
                $workshop = Workshop::where('workshop_name', 'like', '%' . $searchkeyword . '%')->get(['id', 'workshop_name']);
                return response()->json(['status' => TRUE, 'data' => $workshop], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        public
        function getDiligenceByWorkshops($userId)
        {
            $data = WorkshopMeta::with(['workshop:id,workshop_name'])->where('user_id', $userId)->get(['id', 'workshop_id']);
            for ($i = 1; $i <= 12; $i++) {
                $months = date("Y-m-d", strtotime(date('Y-m-01') . " -$i months"));
            }
            $datas = [];
            $meeting = Meeting::with(['presences' => function ($q) use ($userId) {
                $q->where('user_id', $userId)->groupBy('meeting_id');
            }])->whereIn('workshop_id', $data->pluck('workshop_id'))->whereBetween('date', [$months, Carbon::now()->format('Y-m-d')])->get(['id', 'name', 'workshop_id']);

            foreach ($data as $datum) {
                $total = 0;
                $presence = 0;
                foreach ($meeting as $meet) {
                    if (isset($datum->workshop)) {
                        if (isset($datas[$datum->workshop->workshop_name])) {

                            if ($meet->presences != NULL && $meet->workshop_id == $datum->workshop->id) {
                                $total++;
                                if ($meet->presences->presence_status == 'P') {
                                    $presence += 1;
                                }
                                $datas[$datum->workshop->workshop_name]['presence'] = $presence;
                                $datas[$datum->workshop->workshop_name]['total'] = $total;

                            }
                        } else {
                            if ($meet->presences != NULL && $meet->workshop_id == $datum->workshop->id) {
                                $total++;
                                if ($meet->presences->presence_status == 'P') {
                                    $presence += 1;
                                }
                                $datas[$datum->workshop->workshop_name] = ['presence' => $presence, 'total' => $total];
                            }
                        }
                    }
                }
            }
            return response()->json(['status' => TRUE, 'data' => $datas], 200);
        }

        public
        function updateWorkshopGraphic(Request $request)
        {
            try {
                //start transaction for skip the wrong entry
                DB::connection('tenant')->beginTransaction();
                $validator = Validator::make($request->all(), [
                    'workshop_id' => 'required',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422); //validation false return errors
                }
                $workshopGraphic = $this->workshopService->updateWorkshopGraphics($request);


                DB::connection('tenant')->commit();

                return response()->json(['status' => TRUE, 'data' => $workshopGraphic], 200);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        public
        function updateWorkshopPdfGraphic(Request $request)
        {
            //start transaction for skip the wrong entry
//        DB::connection('tenant')->beginTransaction();
            $validator = Validator::make($request->all(), [
                'workshop_id' => 'required',
                'color1'      => 'required',
                'color2'      => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422); //validation false return errors
            }
            $last_rec = Workshop::where('id', $request->workshop_id)->withoutGlobalScopes()->first();
            $graphicPdf = getSettingData('pdf_graphic');
            $pre_data = $this->workshopService->getSetting('pdf', $request->workshop_id);
            $data['pdf'] = ['color1'       => json_decode($request->color1),
                            'color2'       => json_decode($request->color2),
                            'footer_line1' => ($request->footer_line1 != 'undefined') ? $request->footer_line1 : '',
                            'footer_line2' => ($request->footer_line2 != 'undefined') ? $request->footer_line2 : '',
            ];
            if ($request->hasFile('header_logo')) {
                $domain = strtok($_SERVER['SERVER_NAME'], '.');
                $folder = $domain . '/uploads';
                $data['pdf']['header_logo'] = $this->core->fileUploadByS3($request->file('header_logo'), $folder, 'public');
            } else {
                $data['pdf']['header_logo'] = $pre_data->setting['pdf']['header_logo'];
            }
            $last_rec['setting->pdf'] = $data['pdf'];
            $last_rec->save();
            return response()->json(['status' => TRUE, 'data' => $last_rec], 200);
        }

        public function updateWorkshopEmailGraphic(Request $request)
        {
            //start transaction for skip the wrong entry
            // DB::connection('tenant')->beginTransaction();
            $validator = Validator::make($request->all(), [
                'workshop_id' => 'required',
                // 'email_sign' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422); //validation false return errors
            }
            $last_rec = Workshop::where('id', $request->workshop_id)->withoutGlobalScopes()->first();
            $pre_data = $this->workshopService->getSetting('email', $request->workshop_id);
            $graphicPdf = getSettingData('email_graphic');
            $data['email'] = [
                'email_sign' => ($request->email_sign != 'null') ? $request->email_sign : '',
            ];
            if ($request->hasFile('email_top_banner')) {
                $domain = strtok($_SERVER['SERVER_NAME'], '.');
                $folder = $domain . '/uploads';
                $data['email']['top_banner'] = $this->core->fileUploadByS3($request->file('email_top_banner'), $folder, 'public');
            } else {
                $data['email']['top_banner'] = (isset($pre_data->setting['email']['top_banner']) ? $pre_data->setting['email']['top_banner'] : @$graphicPdf->top_banner);
            }
            if ($request->hasFile('email_bottom_banner')) {
                $domain = strtok($_SERVER['SERVER_NAME'], '.');
                $folder = $domain . '/uploads';
                $data['email']['bottom_banner'] = $this->core->fileUploadByS3($request->file('email_bottom_banner'), $folder, 'public');
            } else {
                $data['email']['bottom_banner'] = (isset($pre_data->setting['email']['bottom_banner']) ? $pre_data->setting['email']['bottom_banner'] : @$graphicPdf->bottom_banner);
            }

            $last_rec['setting->email'] = $data['email'];
            $last_rec->save();
            return response()->json(['status' => TRUE, 'data' => $last_rec], 200);
        }

        public function removeWorkshopEmailGraphic(Request $request)
        {
            //start transaction for skip the wrong entry
            // DB::connection('tenant')->beginTransaction();
            $validator = Validator::make($request->all(), [
                'workshop_id' => 'required',
                // 'email_sign' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422); //validation false return errors
            }
            $last_rec = Workshop::where('id', $request->workshop_id)->first();
            $pre_data = $this->workshopService->getSetting('email', $request->workshop_id);

            $data['email'] = [
                // 'email_sign' => json_decode($request->email_sign),
                'email_sign' => ($request->email_sign != 'null') ? $request->email_sign : '',
            ];
            if ($request->type == 'email_top_banner') {
                $data['email']['top_banner'] = '';
            } else {
                $data['email']['top_banner'] = @$pre_data->setting['email']['top_banner'];
            }
            if ($request->type == 'email_bottom_banner') {
                $data['email']['bottom_banner'] = '';
            } else {
                $data['email']['bottom_banner'] = @$pre_data->setting['email']['bottom_banner'];
            }
            $last_rec['setting->email'] = $data['email'];
            $last_rec->save();
            return response()->json(['status' => TRUE, 'data' => $last_rec], 200);
        }

        public function updateWorkshopCustomizedFields(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'workshop_id'         => 'required',
                    'signatory_fname'     => 'required',
                    'signatory_lname'     => 'required',
                    'signatory_possition' => 'required',
                    'signatory_email'     => 'sometimes|required|email',
                    'signatory_phone'     => 'sometimes|nullable',
                    // 'signatory_mobile' => 'required',
                    // 'signatory_deputy_fname' => 'required',
                    // 'signatory_deputy_lname' => 'required',
                    // 'signatory_deputy_possition' => 'required',
                    // 'signatory_deputy_email' => 'required|email',
                    // 'signatory_deputy_phone' => 'required',
                    // 'signatory_deputy_mobile' => 'required',
                    // 'signatory_signature' => 'required',
                    // 'signatory_deputy_signature' => 'required',

                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422); //validation false return errors
                }
                $data = [];
                foreach ($request->all() as $k => $item) {
                    $data[$k] = ($item != NULL) ? $item : '';

                }
                // DB::beginTrnsaction();
                $last_rec = Workshop::where('id', $request->workshop_id)->withoutGlobalScopes()->first();
                // var_dump($last_rec);die;
                unset($data['signatory_deputy_signature']);
                unset($data['signatory_signature']);
                if ($request->hasFile('signatory_deputy_signature')) {
                    $domain = strtok($_SERVER['SERVER_NAME'], '.');
                    $folder = $domain . '/uploads';
                    $data['signatory_deputy_signature'] = $this->core->fileUploadByS3($request->file('signatory_deputy_signature'), $folder, 'public');
                } else {
                    $data['signatory_deputy_signature'] = (!empty($last_rec->signatory) && !empty($last_rec->signatory['signatory_deputy_signature'])) ? $last_rec->signatory['signatory_deputy_signature'] : '';
                }
                if ($request->hasFile('signatory_signature')) {
                    $domain = strtok($_SERVER['SERVER_NAME'], '.');
                    $folder = $domain . '/uploads';
                    $data['signatory_signature'] = $this->core->fileUploadByS3($request->file('signatory_signature'), $folder, 'public');
                } else {
                    $data['signatory_signature'] = (!empty($last_rec->signatory) && !empty($last_rec->signatory['signatory_signature'])) ? $last_rec->signatory['signatory_signature'] : '';
                }
                unset($data['workshop_id']);
                $last_rec->signatory = $data;
                $last_rec->save();
                // DB::commit();
                return response()->json(['status' => TRUE, 'data' => $last_rec], 200);
            } catch (\Exception $e) {
                // DB::rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 200);
            }
        }

        public function getWorkshopTopics()
        {
            try {

                //getting workshops as per Role and id
                if ((Auth::user()->role == 'M1' || Auth::user()->role == 'M0')) {
                    $workshops = Workshop::with(['categories' => function ($a) {
                        $a->where('status', 1)->orderBy('category_name', 'ASC')->select(['id', 'category_name', 'workshop_id']);
                    }])->orderBy('workshop_name', 'ASC')->get(['id', 'workshop_name']);
                } else {
                    $userId = Auth::user()->id;
                    $workshops = Workshop::with(['categories' => function ($a) {
                        $a->where('status', 1)->orderBy('category_name', 'ASC')->select(['id', 'category_name', 'workshop_id']);
                    }])->whereIn('id', function ($query) use ($userId) {
                        $query->select('workshop_id')
                            ->from('workshop_metas')->where('user_id', '=', $userId);
                    })->orderBy('workshop_name', 'ASC')->get(['id', 'workshop_name']);
                }
                return new WorkshopCollection($workshops);

            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        public function getWorkshopByUser($userId)
        {
            return Workshop::with(['workshop_meta' => function ($q) use ($userId) {
                $q->select('id', 'role', 'workshop_id', 'user_id');
                $q->where('user_id', $userId);
            }])->whereHas('workshop_meta', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->select('id', 'workshop_name')->get();
        }

        /*
        * This function is used to set sec+dep if dep is last
        * */
        protected function lastDepRemove($request)
        {
            $meta = WorkshopMeta::where(['workshop_id' => $request->wid, 'role' => 1]);
            if ($meta->count() > 0) {
                $sec = $meta->first();
                if (isset($sec->user_id)) {
                    WorkshopMeta::updateOrCreate(['workshop_id' => $request->wid, 'user_id' => $sec->user_id, 'role' => 2], ['workshop_id' => $request->wid, 'user_id' => $sec->user_id, 'role' => 2]);
                }
            }
        }


        /**
         * @param int $workshopId
         * @param int $role
         * @param int $userId
         * @return int
         * This function is used to remove role of user
         */
        protected function removeWorkshopRole(int $workshopId, int $role, int $userId): int
        {
            return WorkshopMeta::where(['workshop_id' => $workshopId, 'role' => $role, 'user_id' => $userId])->delete();
        }
    }
