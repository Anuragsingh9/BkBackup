<?php
    
    namespace App\Http\Controllers\ImproveMentFour;
    
    use App\Http\Controllers\Controller;
    use App\Model\Skill;
    use App\Model\UserSkill;
    use App\WorkshopMeta;
    use DB;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Validator;
    use App\Presence;
    
    class UserSkillController extends Controller
    {
        private $core, $tenancy, $workshop;
        
        public function __construct()
        {
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);
            $this->core = app(\App\Http\Controllers\CoreController::class);
            $this->workshop = app(\App\Http\Controllers\WorkshopController::class);
        }
        
        /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function index()
        {
            //
        }
        
        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function create()
        {
            //
        }
        
        /**
         * Store a newly created resource in storage.
         *
         * @param \Illuminate\Http\Request $request
         * @return \Illuminate\Http\Response
         */
        public function store(Request $request)
        {
            try {
                //start transaction for skip the wrong entry
                DB::connection('tenant')->beginTransaction();
                if ((isset($request->skill_format_id) && in_array($request->skill_format_id, [7, 13, 21, 22])) && $request->hasFile('value')) {
                  $rules=[
                      'skill_id' => 'required',
                      'value'    => 'required|mimes:jpeg,png,jpg,gif,pdf',
                  ];
                } else {
                    $rules=[
                        'skill_id' => 'required',
                    ];
                }
                $validator = Validator::make($request->all(), $rules,[
                    'image' =>__('validation.custom.file.image'),
                    'mimes' =>__('validation.custom.file.image'),
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                    
                }
                
                //if type have select, image,mandetoryCheckBox
                $addUserSkill = $this->addUserSkill($request);
                if ($addUserSkill) {
                    DB::connection('tenant')->commit();
                    return response()->json(['status' => TRUE, 'data' => $addUserSkill], 200);
                } else {
                    DB::connection('tenant')->rollBack();
                    return response()->json(['status' => FALSE, 'data' => 0], 201);
                }
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 201);
            }
        }
        
        /**
         * Display the specified resource.
         *
         * @param int $id
         * @return \Illuminate\Http\Response
         */
        public function show($id)
        {
            //
        }
        
        /**
         * Show the form for editing the specified resource.
         *
         * @param int $id
         * @return \Illuminate\Http\Response
         */
        public function edit($id)
        {
            //
        }
        
        /**
         * Update the specified resource in storage.
         *
         * @param \Illuminate\Http\Request $request
         * @param int $id
         * @return \Illuminate\Http\Response
         */
        public function update(Request $request, $id)
        {
            try {
                
                //start transaction for skip the wrong entry
                DB::connection('tenant')->beginTransaction();
                if ((isset($request->skill_format_id) && in_array($request->skill_format_id, [7, 13, 21, 22])) && $request->hasFile('value')) {
                    $rules=[
                        'skill_id' => 'required',
                        'value'    => 'required|mimes:jpeg,png,jpg,gif,pdf',
                    ];
                } else {
                    $rules=[
                        'skill_id' => 'required',
                    ];
                }
                $validator = Validator::make($request->all(), $rules,[
                    'image' =>__('validation.custom.file.image'),
                    'mimes' =>__('validation.custom.file.image'),
                ]);
                //validation false return errors
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                    
                }
                
                //if type have select, image,mandetoryCheckBox
                $addUserSkill = $this->addUserSkill($request, $id);
                if ($addUserSkill) {
                    DB::connection('tenant')->commit();
                    return response()->json(['status' => TRUE, 'data' => $addUserSkill], 200);
                } else {
                    DB::connection('tenant')->rollBack();
                    return response()->json(['status' => FALSE, 'data' => 0], 201);
                }
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 201);
            }
        }
        
        /**
         * Remove the specified resource from storage.
         *
         * @param int $id
         * @return \Illuminate\Http\Response
         */
        public function destroy($id)
        {
            //
        }
        
        
        /**
         * Remove the specified resource from storage.
         *
         * @param int $id
         * @return \Illuminate\Http\Response
         */
        public function destroyFle($id)
        {
            UserSkill::where('id', $id)->delete();
            return response()->json(['status' => TRUE, 'data' => 0], 200);
        }
        
        
        public function addUserSkill($request, $id = '')
        {
            
            $value = $this->decideToAdd($request->skill_format_id, $request);
            
            if (!$value) {
                return FALSE;
            } else {
                if (!empty($id)) {
                    $userSkillUpdate = UserSkill::where('id', $id)->update($value);
                    return $userSkill = UserSkill::where('id', $id)->first();
                } else {
                    if (isset($request->for_card_instance)) {
                        $value['for_card_instance'] = isset($request->for_card_instance) ? $request->for_card_instance : 0;
                    } elseif (isset($request->type) && ($request->type == 'candidate')) {
                        $value['for_card_instance'] = isset($request->card_count) ? $request->card_count : 0;
                    }
                    
                    if (isset($value['for_card_instance'])) {
                        $userSkill = UserSkill::create($value);
                    } elseif (isset($value['user_id'])) {
                        $userSkill = UserSkill::updateOrCreate(
                            [
                                'user_id'  => $value['user_id'],
                                'skill_id' => $value['skill_id'],
                            ]
                            , $value);
                    } elseif (isset($value['field_id'])) {
                        $userSkill = UserSkill::updateOrCreate(
                            [
                                'field_id' => $value['field_id'],
                                'type'     => $value['type'],
                                'skill_id' => $value['skill_id'],
                            ]
                            , $value);
                    } else {
                        $userSkill = UserSkill::create($value);
                    }
                    return $userSkill;
                }
            }
        }
        
        public function decideToAdd($format, $request)
        {
            
            if (isset($request->type) && ($request->type == 'company' || $request->type == 'contact' || $request->type == 'union' || $request->type == 'instance' || $request->type == 'press' || $request->type == 'referrer' || $request->type == 'candidate')) {
                switch ($format) {
                    case 7:
                        if ($request->hasFile('value')) {
                            $domain = strtok($_SERVER['SERVER_NAME'], '.');
                            $folder = $domain . '/uploads/skills';
                            $filename = $this->core->fileUploadByS3($request->file('value'), $folder, 'public');
                        } else {
                            return FALSE;
                        }
                        return ['field_id' => $request->field_id, 'type' => $request->type, 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'file_input' => $filename];
                        break;
                    
                    case 8:
                        return ['field_id' => $request->field_id, 'type' => $request->type, 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'select_input' => $request->value];
                        break;
                    case 12:
                        return ['field_id' => $request->field_id, 'type' => $request->type, 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'mandatory_checkbox_input' => $request->value];
                        break;
                    case 13:
                        if ($request->hasFile('value')) {
                            $domain = strtok($_SERVER['SERVER_NAME'], '.');
                            $folder = $domain . '/uploads/skills';
                            $filename = $this->core->fileUploadByS3($request->file('value'), $folder, 'public');
                        } else {
                            return FALSE;
                        }
                        return ['field_id' => $request->field_id, 'type' => $request->type, 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'mandatory_file_input' => $filename];
                        break;
                    case 14:
                        return ['field_id' => $request->field_id, 'type' => $request->type, 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'comment_text_input' => $request->value];
                        break;
                    case 15:
                        return ['field_id' => $request->field_id, 'type' => $request->type, 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'address_text_input' => $request->value];
                        break;
                    case 11:
                        return ['field_id' => $request->field_id, 'type' => $request->type, 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'date_input' => $request->value];
                        break;
                    case 10:
                        return ['field_id' => $request->field_id, 'type' => $request->type, 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'long_text_input' => $request->value];
                        break;
                    case 9:
                        return ['field_id' => $request->field_id, 'type' => $request->type, 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'numerical_input' => $request->value];
                        break;
                    case 6:
                        return ['field_id' => $request->field_id, 'type' => $request->type, 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'text_input' => $request->value];
                        break;
                    case 5:
                        return ['field_id' => $request->field_id, 'type' => $request->type, 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'scale_5_input' => $request->value];
                        break;
                    case 4:
                        return ['field_id' => $request->field_id, 'type' => $request->type, 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'scale_10_input' => $request->value];
                        break;
                    case 3:
                        return ['field_id' => $request->field_id, 'type' => $request->type, 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'percentage_input' => $request->value];
                        break;
                    case 2:
                        return ['field_id' => $request->field_id, 'type' => $request->type, 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'yes_no_input' => $request->value];
                        break;
                    case 17:
                        return ['field_id' => $request->field_id, 'type' => $request->type, 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'mandatory_acceptance_input' => $request->value];
                        break;
                    case 20:
                        if (isset($request->value) && !empty($request->value)) {
                            $domain = strtok($_SERVER['SERVER_NAME'], '.');
                            $folder = $domain . '/uploads/skills';
                            $filename = $this->core->fileUploadByS3($request->value, $folder, 'public');
                        } else {
                            return FALSE;
                        }
                        return ['field_id' => $request->field_id, 'type' => $request->type, 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'file_input' => $filename];
                        break;
                    case 19:
                        return ['field_id' => $request->field_id, 'type' => $request->type, 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'select_input' => $request->value];
                        break;
                    case 21:
                        
                        if ($request->hasFile('value')) {
                            $domain = strtok($_SERVER['SERVER_NAME'], '.');
                            $folder = $domain . '/uploads/skills';
                            $filename = $this->core->fileUploadByS3($request->file('value'), $folder, 'public');
                        } else {
                            return FALSE;
                        }
                        return ['field_id' => $request->field_id, 'type' => $request->type, 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'file_input' => $filename];
                        break;
                    case 22:
                        
                        if ($request->hasFile('value')) {
                            $domain = strtok($_SERVER['SERVER_NAME'], '.');
                            $folder = $domain . '/uploads/skills';
                            $filename = $this->core->fileUploadByS3($request->file('value'), $folder, 'public');
                        } else {
                            return FALSE;
                        }
                        return ['field_id' => $request->field_id, 'type' => $request->type, 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'file_input' => $filename];
                        break;
                    case 23:
                        return ['field_id' => $request->field_id, 'type' => $request->type, 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'numerical_input' => $request->value];
                        break;
                    default:
                        return ['field_id' => $request->field_id, 'type' => $request->type, 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'checkbox_input' => $request->value];
                }
            } else {
                switch ($format) {
                    case 7:
                        if ($request->hasFile('value')) {
                            $domain = strtok($_SERVER['SERVER_NAME'], '.');
                            $folder = $domain . '/uploads/skills';
                            $filename = $this->core->fileUploadByS3($request->file('value'), $folder, 'public');
                        } else {
                            return FALSE;
                        }
                        return ['user_id' => (isset($request->user_id) ? $request->user_id : Auth::user()->id), 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'file_input' => $filename];
                        break;
                    case 8:
                        return ['user_id' => (isset($request->user_id) ? $request->user_id : Auth::user()->id), 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'select_input' => $request->value];
                        break;
                    case 12:
                        return ['user_id' => (isset($request->user_id) ? $request->user_id : Auth::user()->id), 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'mandatory_checkbox_input' => $request->value];
                        break;
                    case 13:
                        if ($request->hasFile('value')) {
                            $domain = strtok($_SERVER['SERVER_NAME'], '.');
                            $folder = $domain . '/uploads/skills';
                            $filename = $this->core->fileUploadByS3($request->file('value'), $folder, 'public');
                        } else {
                            return FALSE;
                        }
                        return ['user_id' => (isset($request->user_id) ? $request->user_id : Auth::user()->id), 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'mandatory_file_input' => $filename];
                        break;
                    case 14:
                        return ['user_id' => (isset($request->user_id) ? $request->user_id : Auth::user()->id), 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'comment_text_input' => $request->value];
                        break;
                    case 15:
                        return ['user_id' => (isset($request->user_id) ? $request->user_id : Auth::user()->id), 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'address_text_input' => $request->value];
                        break;
                    case 11:
                        return ['user_id' => (isset($request->user_id) ? $request->user_id : Auth::user()->id), 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'date_input' => $request->value];
                        break;
                    case 10:
                        return ['user_id' => (isset($request->user_id) ? $request->user_id : Auth::user()->id), 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'long_text_input' => $request->value];
                        break;
                    case 9:
                        return ['user_id' => (isset($request->user_id) ? $request->user_id : Auth::user()->id), 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'numerical_input' => $request->value];
                        break;
                    case 6:
                        return ['user_id' => (isset($request->user_id) ? $request->user_id : Auth::user()->id), 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'text_input' => $request->value];
                        break;
                    case 5:
                        return ['user_id' => (isset($request->user_id) ? $request->user_id : Auth::user()->id), 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'scale_5_input' => $request->value];
                        break;
                    case 4:
                        return ['user_id' => (isset($request->user_id) ? $request->user_id : Auth::user()->id), 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'scale_10_input' => $request->value];
                        break;
                    case 3:
                        return ['user_id' => (isset($request->user_id) ? $request->user_id : Auth::user()->id), 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'percentage_input' => $request->value];
                        break;
                    case 2:
                        return ['user_id' => (isset($request->user_id) ? $request->user_id : Auth::user()->id), 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'yes_no_input' => $request->value];
                        break;
                    case 17:
                        return ['user_id' => (isset($request->user_id) ? $request->user_id : Auth::user()->id), 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'mandatory_acceptance_input' => $request->value];
                        break;
                    case 20:
                        if (isset($request->value) && !empty($request->value)) {
                            $domain = strtok($_SERVER['SERVER_NAME'], '.');
                            $folder = $domain . '/uploads/skills';
                            $filename = $this->core->fileUploadByS3($request->value, $folder, 'public');
                        } else {
                            return FALSE;
                        }
                        return ['field_id' => $request->field_id, 'type' => $request->type, 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'file_input' => $filename];
                        break;
                    case 19:
                        return ['user_id' => (isset($request->user_id) ? $request->user_id : Auth::user()->id), 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'select_input' => $request->value];
                        break;
                    case 21:
                        if ($request->hasFile('value')) {
                            $domain = strtok($_SERVER['SERVER_NAME'], '.');
                            $folder = $domain . '/uploads/skills';
                            $filename = $this->core->fileUploadByS3($request->file('value'), $folder, 'public');
                        } else {
                            return FALSE;
                        }
                        return ['user_id' => (isset($request->user_id) ? $request->user_id : Auth::user()->id), 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'file_input' => $filename];
                        break;
                    case 22:
                        if ($request->hasFile('value')) {
                            $domain = strtok($_SERVER['SERVER_NAME'], '.');
                            $folder = $domain . '/uploads/skills';
                            $filename = $this->core->fileUploadByS3($request->file('value'), $folder, 'public');
                        } else {
                            return FALSE;
                        }
                        return ['user_id' => (isset($request->user_id) ? $request->user_id : Auth::user()->id), 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'file_input' => $filename];
                        break;
                    case 23:
                        return ['user_id' => (isset($request->user_id) ? $request->user_id : Auth::user()->id), 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'numerical_input' => $request->value];
                        break;
                    default:
                        return ['user_id' => (isset($request->user_id) ? $request->user_id : Auth::user()->id), 'created_by' => ((Auth::user() != NULL) ? (Auth::user()->id) : 0), 'skill_id' => $request->skill_id, 'checkbox_input' => $request->value];
                }
            }
            
        }
        
        public function getUserSkill(Request $request)
        {
            
            $memberData = $this->workshop->getWorkshopMemberArray($request);
            $data = WorkshopMeta::where('workshop_id', $request->wid)->where('role', '!=', 3)->groupBy('user_id')->get(['id', 'user_id', 'workshop_id']);
            $skills = Skill::where('skill_tab_id', $request->tab_id)->where('is_valid', 1)->with('skillSelect', 'skillFormat', 'skillMeta')->with(['allUserSkills' => function ($a) use ($data) {
                $a->whereIn('user_id', $data->pluck('user_id'));
            }])->orderBy('sort_order', 'asc')->get();
            return response()->json(['status' => TRUE, 'data' => $skills], 200);
        }
        
        public function getPresenceUserSkill(Request $request)
        {
            $user_id = Presence::where('meeting_id', $request->mid)->get(['user_id'])->pluck('user_id');
            $data = WorkshopMeta::where('workshop_id', $request->wid)->whereIn('user_id', $user_id)->where('role', '!=', 3)->groupBy('user_id')->get(['id', 'user_id', 'workshop_id']);
            $skills = Skill::whereHas('skillTab', function ($q) {
                $q->where('added_to_presence', TRUE);
            })->where('is_valid', 1)->with('skillSelect', 'skillFormat', 'skillMeta')->with(['allUserSkills' => function ($a) use ($data) {
                $a->whereIn('user_id', $data->pluck('user_id'));
            }])->orderBy('sort_order', 'asc')->get();
            return response()->json(['status' => TRUE, 'data' => $skills], 200);
        }
    }
