<?php
    
    namespace Modules\Qualification\Http\Controllers;
    
    
    use App\Model\Skill;
    use App\Model\UserMeta;
    use App\Model\UserSkill;
    use App\User;
    use App\Workshop;
    use App\WorkshopMeta;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Routing\Controller;
    use Illuminate\Support\Facades\Auth;
    use Modules\Qualification\Entities\Step;
    use Modules\Qualification\Entities\StepConditional;
    use Modules\Qualification\Services\RegistrationService;
    use Modules\Qualification\Services\StepService;
    use Validator;
    use DB;
//use Modules\Qualification\Services\StepService;
    use Modules\Qualification\Entities\CandidateCard;
    use Modules\Qualification\Entities\CandidateField;
    
    
    class StepController extends Controller
    {
        /**
         * Display a listing of the resource.
         * @return Response
         */
        
        public function __construct()
        {
            $this->core = app(\App\Http\Controllers\CoreController::class);
            $this->stepService = StepService::getInstance();
        }
        
        public function index()
        {
            try {
                //feth the list of steps
                $step = Step::orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->get(['id', 'name', 'is_conditional', 'is_final_step', 'sort_order', 'description', 'is_final_step']);
                if (!$step) {
                    return Response()->json(['status' => FALSE, 'msg' => 'Data Not Found', 'data' => []], 200);
                }
                return response()->json(['status' => TRUE, 'data' => $step], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        /**
         * Show the form for creating a new resource.
         * @return Response
         */
        public function create()
        {
            return view('qualification::create');
        }
        
        /**
         * Store a newly created resource in storage.
         * @param Request $request
         * @return Response
         */
        public function store(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'name'                    => 'required|regex:/[a-zA-Z0-9\s]+/|min:3|max:255',
//                'description' => 'required|regex:/[a-zA-Z0-9\s]+/|min:3|max:255',
                    'is_conditional'          => 'required|in:0,1',
                    'is_final_step'           => 'required|in:0,1',
                    'conditional_checkbox_id' => 'required_if:is_conditional,1|exists:tenant.skills,id',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                //transaction start
//            DB::connection('tenant')->beginTransaction();
                $step = DB::transaction(function () use ($request) {
                    /**
                     * check is_final_condition value
                     * if is_final_value =1 then, Add Butto_text value in data base
                     * if is_final_value = 0 then ,Button_text = null
                     */
                    $stepCount = Step::count() + 1;
                    $step = Step::create([
                        'name'           => $request->name,
                        'description'    => isset($request->description) ? $request->description : '',
                        'is_conditional' => $request->is_conditional,
                        'sort_order'     => $stepCount,
                        'button_text'    => $request->button_text,
                        'is_final_step'  => isset($request->is_final_step) ? $request->is_final_step : 0,
                    ]);
                    if ($request->is_conditional == 1) {
                        StepConditional::create([
                            'step_id'                 => $step->id,
                            'conditional_checkbox_id' => $request->conditional_checkbox_id,
                            'is_checked'              => $request->is_checked,
                        ]);
                    }
                    //add default fields for step if these are not first 2 steps
                    if ((isset($request->is_final_step) && $request->is_final_step == 1) /*&& config('accountName') == 'cartetppro'*/) {
                        $response = $this->stepService->createInitialStepFields($step->id);
                        
                        if (is_bool($response)) {
                            return $step;
                        } else {
                            return $response;
                        }
                        
                    } else {
                        return $step;
                    }
                });
                if (is_object($step))
                    return response()->json(['status' => TRUE, 'msg' => 'Record Added Successfully', 'data' => $step], 200);
                else
                    return response()->json(['status' => FALSE, 'msg' => $step], 500);
                //transaction commit
//            DB::connection('tenant')->commit();
            
            } catch (\Exception $e) {
                //transaction rollback
//            DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        /**
         * Show the specified resource.
         * @return Response
         */
        public function show()
        {
            return view('qualification::show');
        }
        
        /**
         * Show the form for editing the specified resource.
         * @return Response
         */
        public function edit($id)
        {
            try {
                $step = Step::with('conditional:id,is_checked,conditional_checkbox_id,step_id')->find($id);
                if (!$step) {
                    return response()->json(['status' => FALSE, 'msg' => 'Data Not Found', 'data' => []], 200);
                }
                return response()->json(['status' => TRUE, 'data' => $step]);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        /**
         * Update the specified resource in storage.
         * @param Request $request
         * @return Response
         */
        public function update(Request $request, $id)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'name'           => 'required|regex:/[a-zA-Z0-9\s]+/|min:3|max:255',
//                'description' => 'sometimes|required|regex:/[a-zA-Z0-9\s]+/|min:3|max:255',
                    'is_conditional' => 'sometimes|required|in:0,1',
                    'is_final_step'  => 'required|in:0,1',
                ]);
                
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                //transaction start
                DB::connection('tenant')->beginTransaction();
                /**
                 * check is_final_condition value
                 * if is_final_value =1 then, Add Butto_text value in data base
                 * if is_final_value = 0 then ,Button_text = null
                 */
                $step = Step::where('id', $id)->with('conditional:id,is_checked,conditional_checkbox_id,step_id')->first();
                
                $update = [
                    'name'           => $request->name,
                    'description'    => isset($request->description) ? $request->description : $step->description,
                    'is_conditional' => $request->is_conditional,
                    'button_text'    => isset($request->button_text) ? $request->button_text : $step->button_text,
                    'is_final_step'  => isset($request->is_final_step) ? $request->is_final_step : $step->is_final_step,
                ];
                $step->update($update);
                if ($request->is_conditional == 1) {
                    StepConditional::updateOrCreate(['step_id' => $step->id], [
                        'conditional_checkbox_id' => $request->conditional_checkbox_id,
                        'is_checked'              => $request->is_checked,
                    ]);
                }
                
                if (!$step) {
                    return Response()->json(['status' => FALSE, 'msg' => 'Data Not Found For Update', 'data' => []], 200);
                }
                //transaction commit
                DB::connection('tenant')->commit();
                return response()->json(['status' => TRUE, 'msg' => 'Record Updated Successfully', 'data' => $step], 200);
            } catch (\Exception $e) {
                //transaction rollback
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
            DB::connection('tenant')->beginTransaction();
            try {
                $step = Step::find($id);
                if (!$step) {
                    return Response()->json(['status' => FALSE, 'msg' => 'Data Not Found For Delete'], 200);
                }
                // delete related
                $step->stepFields()->delete();
                $step->conditional()->delete();
                $step->delete();
                DB::connection('tenant')->commit();
                return response()->json(['status' => TRUE, 'msg' => 'Record Deleted Successfully'], 200);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function stepDrag(Request $request)
        {
            $data = json_decode($request->data);
            if (count($data) > 0) {
                foreach ($data as $k => $val) {
                    $setting = Step::where('id', $val->id)->update(['sort_order' => ($k + 1)]);
                }
                $steps = Step::orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->get(['id', 'name', 'is_conditional', 'is_final_step', 'sort_order', 'description', 'is_final_step']);
                return response()->json(['status' => TRUE, 'data' => $steps], 200);
            }
        }
        
        public function getSteps()
        {
            try {
                //feth the list of steps
                $steps = Step::orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->/*with('fields:qualification_fields.id,name,short_name')->*/
                get(['id', 'name', 'description', 'is_conditional', 'is_final_step', 'button_text', 'sort_order']);
                if (!$steps) {
                    return Response()->json(['status' => FALSE, 'msg' => 'no data found', 'data' => []], 200);
                } else {
                    $currentStep = UserMeta::where('user_id', Auth::user()->id)->first(['current_step_id']);
                    return response()->json(['status' => TRUE, 'data' => ['steps' => $steps, 'current_step_id' => isset($currentStep->current_step_id) ? $currentStep->current_step_id : getFirstStepId()]], 200);
                }
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function getStepsCandidate($candidateId, $isAdmin = 0, $status = 0, $cardCount = 0)
        {
            
            try {
                $currentStep = UserMeta::where('user_id', $candidateId)->first(['current_step_id', 'is_final_save']);
                $workshopId = WorkshopMeta::where('user_id', $candidateId)->first(['workshop_id']);
                //$currentStep = UserMeta::where('user_id', $candidateId)->first(['current_step_id', 'is_final_save']);
                if (!$isAdmin && isset($currentStep->is_final_save) && ($currentStep->is_final_save >= 1 && $currentStep->is_final_save < 3)) {
                    //  $workshopId = WorkshopMeta::where('user_id', $candidateId)->first(['workshop_id']);
                    // $currentStep['workshop_id'] = (!empty($workshopId) ? $workshopId->workshop_id : 0);
                    $currentStep->workshop_id = (!empty($workshopId) ? $workshopId->workshop_id : 0);
                    return response()->json(['status' => TRUE, 'data' => $currentStep], 200);
                }
                
                //feth the list of steps
                $steps = Step::orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->with(['conditional:id,is_checked,conditional_checkbox_id,step_id', 'domainCheckbox' => function ($b) {
                    $b->select('skills.id', 'skill_tab_id', 'name', 'short_name', 'is_valid', 'is_mandatory', 'skill_format_id', 'is_unique', 'is_conditional', 'is_qualifying', 'tooltip_en', 'tooltip_fr');
                }])->where(function ($query) use ($candidateId) {
                    $query->whereHas('domainCheckbox.userSkill', function ($q) use ($candidateId) {
                        $q->where('field_id', ($candidateId))->where('type', 'candidate')->where('checkbox_input', 1);
                    })->orWhere('is_final_step', 0);
                })->get(['id', 'name', 'description', 'is_conditional', 'is_final_step', 'button_text', 'sort_order']);
                
                $finalArray = [];
                $existField = [];
                $cardCount = 0;
                foreach ($steps as $item) {
                    //checking Step is Conditional
                    if ($item->is_conditional) {
                        if (!empty($item->conditional)) {
                            //getting conditional Skill Data with User
                            $skill = UserSkill::where('field_id', $candidateId)->where('skill_id', $item->conditional->conditional_checkbox_id)->first(['id', 'checkbox_input']);
                            //checking conditional step have conditional skill should checked condition
                            if ($item->conditional->is_checked) {
                                //checking if skill is check then show data
                                if (isset($skill->checkbox_input) && $skill->checkbox_input) {
                                    $finalArray[] = $item;
                                } /*else {
                            return Response()->json(['status' => false, 'msg' => 'Please Fill Conditional Field First', 'data' => []], 200);
                        }*/
                            }
                        } else {
                            $finalArray[] = $item;
                        }
                    } else {
                        $finalArray[] = $item;
                    }
                }
                
                if (count($finalArray) == 0) {
                    return Response()->json(['status' => FALSE, 'msg' => 'no data found', 'data' => []], 200);
                } else {
                    $existField = [];
                    $cardData = CandidateCard::where('user_id', $candidateId)->get(['id', 'date_of_validation as created_at']);
                    
                    if ($cardData->count()) {
                        $existField = CandidateField::where('user_id', $candidateId)->get(['qualification_field_id'])->pluck('qualification_field_id');
                    }
                    $user = User::select('fname', 'lname', 'email', 'phone', 'address', 'postal')->find($candidateId);
                    $finalArray[0]->fname = $user->fname;
                    $finalArray[0]->lname = $user->lname;
                    $finalArray[0]->email = $user->email;
                    $finalArray[0]->phone = $user->phone;
                    $finalArray[0]->mobile = $user->mobile;
                    $finalArray[0]->address = $user->address;
                    $finalArray[0]->zip_code = $user->postal;
                    if ($isAdmin && ($status == 3)) {
                        //as in this always status 3 will come
                        $isFinalSave = 3;
                    } else {
                        $isFinalSave = isset($currentStep->is_final_save) ? $currentStep->is_final_save : 0;
                    }
                    return response()->json([
                        'status' => TRUE, 'data' => [
                            'steps'           => $finalArray,
                            'current_step_id' => isset($currentStep->current_step_id) ? $currentStep->current_step_id : getFirstStepId(),
                            'card_data'       => $cardData,
                            'exists_fields'   => $existField,
                            //check final save for first time we get last usermeta id
                            // so vijay change this to 0
                            'is_final_save'   => $isFinalSave,
                            'workshop_id'     => (!empty($workshopId) ? $workshopId->workshop_id : 0),
                        ]], 200);
                }
            } catch
            (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function getStepFields($id, $candidateId)
        {
            try {
                $validator = Validator::make(['id' => $id], [
                    'id' => 'required|exists:tenant.qualification_steps,id',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                //===============================
                /*  $carCount=CandidateCard::where('user_id',$candidateId)->count();
                  $fourRenewal = [5, 9, 13, 17, 21, 25];
                  $renewal = range(1, 25);
                  $forIns = 1;
                  if ((($carCount) % 4 == 0) && (($carCount > 0))) {
                      $key = array_search(($carCount + 1), $fourRenewal);
                      $forIns = $fourRenewal[$key];
                  } else {
                      $key = array_search(($carCount + 1), $renewal);
                      $forIns = 1;
                      foreach ($fourRenewal as $k => $val) {
                          if ($renewal[$key] <= $val) {
                              if ($renewal[$key] >= 5) {
                                  if ($k != 0)
                                      $forIns = $fourRenewal[$k - 1];
                                  else
                                      $forIns = $fourRenewal[$k];
                                  break;
                              } else {
                                  $forIns = 1;
                                  break;
                              }
                          }
                      }
                  }*/
                //=================================
                
                //'sort_order',
                $steps = Step::where('id', $id)->with(['fields'                      => function ($b) {
                    $b->select('skills.id', 'skill_tab_id', 'name', 'short_name', 'is_valid', 'is_mandatory', 'skill_format_id', 'is_unique', 'is_conditional', 'is_qualifying', 'tooltip_en', 'tooltip_fr');
                }, 'fields.domainCheckbox:id,step_id,skill_id', 'fields.skillFormat' => function ($a) {
                    $a->select('id', 'name_en', 'name_fr');
                }, 'fields.userSkill'                                                => function ($q) use ($candidateId) {
                    $q->where('field_id', ($candidateId))->where('type', 'candidate')/*->where('for_card_instance',$forIns)*/
                    ;
                }, 'fields.UserHaveManySkills'                                       => function ($q) use ($candidateId) {
                    $q->where('field_id', ($candidateId))->where('type', 'candidate');
                }, 'fields.skillImages', 'fields.skillSelect', 'fields.skillCheckBox', 'fields.skillMeta', 'fields.skillCheckBoxAcceptance', 'fields.conditionalSkill'])->first(['id', 'name', 'description', 'is_conditional', 'is_final_step', 'button_text', 'sort_order']);
                
                if (!$steps) {
                    return Response()->json(['status' => FALSE, 'msg' => 'no data found', 'data' => []], 200);
                } else {
                    $fields = [];
                    //=============//
                    foreach ($steps->fields as $item) {
                        if ($item->is_conditional) {
                            if (isset($item->conditionalSkill)) {
                                //getting conditional Skill Data with User
                                $skill = UserSkill::where('field_id', $candidateId)->where('skill_id', $item->conditionalSkill->conditional_checkbox_id)->first(['id', 'checkbox_input', 'skill_id']);
                                //checking conditional step have conditional skill should checked condition
                                if ($item->conditionalSkill->is_checked) {
                                    //checking if skill is check then show data
                                    if (isset($skill->checkbox_input) && $skill->checkbox_input) {
                                        $fields[] = $item;
                                    } /*else {

                                }*/
                                } else {
                                    if (isset($skill->checkbox_input) && $skill->checkbox_input == 0) {
                                        $fields[] = $item;
                                    } elseif (!isset($skill->checkbox_input)) {
                                        $fields[] = $item;
                                    }
                                }
                            } else {
                                $fields[] = $item;
                            }
                        } else {
                            $fields[] = $item;
                        }
                    }
                    unset($steps['fields']);
                    $steps->fields = $fields;
                    //                dd($steps->fields);
                    //============//
                    
                    if ($steps->sort_order == 1) {
                        $user = User::select("fname", 'lname', "email", "phone", "address", 'mobile', 'postal')->find($candidateId);
                        $steps->fname = $user->fname;
                        $steps->lname = $user->lname;
                        $steps->email = $user->email;
                        $steps->phone = $user->phone;
                        $steps->address = $user->address;
                        $steps->mobile = $user->mobile;
                        $steps->zip_code = $user->postal;
                    }
                    $meta = UserMeta::updateOrCreate(['user_id' => $candidateId], ['user_id' => $candidateId, 'current_step_id' => $id]);
                    return response()->json(['status' => TRUE, 'data' => $steps], 200);
                }
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function getAdminStepFields($id)
        {
            try {
                $validator = Validator::make(['id' => $id], [
                    'id' => 'required|exists:tenant.qualification_steps,id',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                //feth the list of steps
                $steps = Step::where('id', $id)->with(['fields' => function ($b) {
                    $b->select('skills.id', 'skill_tab_id', 'name', 'short_name', 'is_valid', 'is_mandatory', 'skill_format_id', 'is_unique', 'skills.sort_order', 'is_conditional', 'is_qualifying', 'tooltip_en', 'tooltip_fr');
                }, 'fields.skillFormat'                         => function ($a) {
                    $a->select('id', 'name_en', 'name_fr');
                }, 'fields.skillImages', 'fields.skillSelect', 'fields.skillCheckBox', 'fields.skillMeta', 'fields.skillCheckBoxAcceptance'])->first(['id', 'name', 'description', 'is_conditional', 'is_final_step', 'button_text', 'sort_order']);
                
                if (!$steps) {
                    return Response()->json(['status' => FALSE, 'msg' => 'no data found', 'data' => []], 200);
                } else {
                    return response()->json(['status' => TRUE, 'data' => isset($steps->fields) ? $steps->fields : []], 200);
                }
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        
        /**
         * @param Request $request
         * this function is used for save candidate last step means last point of his registration option
         */
        public function saveFinalStep(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'step_id'      => 'exists:tenant.qualification_steps,id|required_if:status,1',
                    'status'       => 'required|in:0,1,2,3',
                    'candidate_id' => 'required|exists:tenant.users,id',
                
                ]);
                
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                
                $status = 0;
                $mail = 1;
                $candidateId = (isset($request->candidate_id) ? $request->candidate_id : Auth::user()->id);
                $data = [];
                if (isset($request->candidate_id)) {
                    $user = User::with('userCards')->find($request->candidate_id);
                } else
                    $user = Auth::user()->load('userCards');
                
                $workshopId = WorkshopMeta::where('user_id', $candidateId)->first();
                switch ($request->status) {
                    case 1:
                        if (isset($user->userCards) && count($user->userCards) >= 1) {
                            if ((count($user->userCards)) % 4 == 0) {
                                $status = 1;
                                $mail = 1;
                            } else {
                                $status = 2;
                                $mail = 0;
                            }
                            
                        } else {
                            $status = 1;
                            $mail = 1;
                        }
                        
                        $condition = ['user_id' => $candidateId];
                        $data = ['user_id' => $candidateId, 'current_step_id' => $request->step_id, 'is_final_save' => $status, 'created_at' => Carbon::now()->format('Y-m-d h:i:s')];
                        $workshop_data = Workshop::with('meta')->withoutGlobalScopes()->find($workshopId->workshop_id);
                        //this $mail variable checks that mail should be send or not
                        if ($mail) {
                            if (session()->has('lang')) {
                                // $key = 'email_request_ready_notification_to_secratory_' . session()->get('lang');
                                $key = 'a_request_prevalidate_' . session()->get('lang');
                            } else {
                                // $key = 'email_request_ready_notification_to_secratory_FR';
                                $key = 'a_request_prevalidate_FR';
                            }
                            $dataMail = $this->getMailData($workshop_data, $key, $candidateId);
                            $subject = $dataMail['subject'];
                            $wkadmin = [];
                            if (isset($workshop_data->meta)) {
                                $meta = $workshop_data->meta->whereIN('role', [1, 2]);
        
                                $meta->map(function ($v, $k) use (&$wkadmin) {
                                    if (isset($v->user->email)) {
                                        $wkadmin[] = $v->user->email;
                                    }
                                });
                            } else {
                                $wkadmin = [Auth::user()->email];
                            }
                            $url = route('redirect-app-url', ['url' => str_rot13('qualification/' . $workshopId->workshop_id . '/candidates')]);// . $user->id . '/review/1/secretary'
                            $mailData['mail'] = ['subject' => ($subject), 'emails' => $wkadmin, 'workshop_data' => $workshop_data, 'template_setting' => $key, 'url' => $url, 'candidateId' => $candidateId];
                            $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                        }
                        break;
                    case 2:
                        $status = 2;
                        $condition = ['user_id' => $request->candidate_id];
                        $data = ['user_id' => $request->candidate_id, 'is_final_save' => $status, 'final_by' => Auth::user()->id];
                        $workshop_data = Workshop::with('meta')->withoutGlobalScopes()->find($workshopId->workshop_id);
                        if (session()->has('lang'))
                            $key = 'expert_request_is_ready_for_option_' . session()->get('lang');
                        else
                            $key = 'expert_request_is_ready_for_option_FR';
                        $dataMail = $this->getMailData($workshop_data, $key, $request->candidate_id);
                        $subject = $dataMail['subject'];
                        // $wkadmin = isset($workshop_data->meta) ? $workshop_data->meta->where('role', 2)->first() : Auth::user()->email;
                        $wkadmin = isset($workshop_data->meta) ? $workshop_data->meta->where('role', 0)->pluck('user.email')->toArray() : [];
                        // var_dump($wkadmin);die;
                        $url = route('redirect-app-url', ['url' => str_rot13('qualification/' . $workshopId->workshop_id . '/candidates')]);// . $user->id . '/review/2/secretary'
                        $mailData['mail'] = ['subject' => ($subject), 'emails' => $wkadmin, 'workshop_data' => $workshop_data, 'template_setting' => $key, 'candidateId' => $request->candidate_id, 'url' => $url, 'candidateId' => $request->candidate_id];
                        if (count($wkadmin) > 0) {
                        $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                        }
                        break;
                    case 3:
                        DB::connection('tenant')->beginTransaction();
                        $status = 3;
                        $condition = ['user_id' => $request->candidate_id];
                        
                        
                        $workshop_data = Workshop::with('meta')->withoutGlobalScopes()->find($workshopId->workshop_id);
                        $registrationService = RegistrationService::getInstance();
                        //check that any domain is granted or not
                        $card_count = isset($user->userCards) ? count($user->userCards) : CandidateCard::where('user_id', $request->candidate_id)->count();
                        $granted = $this->stepService->getGrantedDomain($request->candidate_id, $card_count);
                        // here is creating entery for meta table for updated status we are adding new status 5 for rejected
                        $data = ['user_id' => $request->candidate_id, 'is_final_save' => ($granted == 0) ? config('constants.REJECTED_STATUS') : $status, 'final_by' => Auth::user()->id, 'saved_at' => Carbon::now()->format('Y-m-d h:i:s')];
                        if ($granted) {
                            $dataMail = $this->getMailData($workshop_data, 'certificate_granted_' . userLang(), $request->candidate_id, $registrationService->getDeliveryDate($user->id));
                            $subject = $dataMail['subject'];
                            $redirectUrl = url('/#/qualification/registration-form');
                            $mailData['mail'] = ['subject' => ($subject), 'emails' => [$user->email], 'workshop_data' => $workshop_data, 'template_setting' => 'certificate_granted_' . userLang(), 'date' => $registrationService->getDeliveryDate($user->id), 'domain' => getGrantedDomain($user->id), 'candidateId' => $request->candidate_id, 'url' => $redirectUrl];
                            $val = $this->core->SendMassEmail($mailData, 'dynamic_workshop_template');
                        //this is final submit by admin so we will create the card entry
                            $card = $this->stepService->createCard(['user_id' => $request->candidate_id, 'wid' => $workshopId->workshop_id]);
                        $check = $this->stepService->copysteps($request->candidate_id);
                        $checking = $this->stepService->copyPersonalChecking($request->candidate_id, $card);
                        if ($check == FALSE) {
                            DB::connection('tenant')->rollback();
                            return response()->json(['status' => FALSE, 'msg' => 'Something Wrong Happend'], 200);
                        }
                        }
                        DB::connection('tenant')->commit();
                        break;
                    default:
                        $status = 0;
                        $condition = ['user_id' => $request->candidate_id];
                        $data = ['user_id' => $request->candidate_id, 'is_final_save' => $status, 'final_by' => Auth::user()->id];
                }
                if (!empty($data)) {
                    
                    $final = UserMeta::updateOrCreate($condition, $data);
                    return response()->json(['status' => TRUE, 'data' => $final], 200);
                } else {
                    return response()->json(['status' => FALSE, 'msg' => 'Something Wrong Happend'], 200);
                }
                
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        function getMailData($workshop_data, $key, $cid = 0, $date = [])
        {
            
            $currUserFname = Auth::user()->fname;
            $currUserLname = Auth::user()->lname;
            $currUserEmail = Auth::user()->email;
            $settings = getSettingData($key);
            $getOrgDetail = getOrgDetail();
            $member = workshopValidatorPresident($workshop_data);
            $wsetting = getWorkshopSettingData($workshop_data->id);
            $WorkshopSignatory = getWorkshopSignatoryData($workshop_data->id);
            $user = getCandidateUser($cid);
            $domain = getGrantedDomain($cid);
            // var_dump($date,$domain);die;
            $keywords = [
                '[[UserFirsrName]]', '[[UserLastName]]', '[[UserEmail]]', '[[WorkshopLongName]]', '[[WorkshopShortName]]', '[[WorkshopPresidentFullName]]',
                // '[[WorkshopvalidatorFullName]]',
                '[[ValidatorEmail]]', '[[PresidentEmail]]', '[[OrgName]]',
                
                '[[SignatoryFname]]',
                '[[Signatorylname]]',
                '[[SignatoryPossition]]',
                '[[SignatoryEmail]]',
                '[[SignatoryPhone]]',
                '[[SignatoryMobile]]',
                '[[candidateFN]]',
                '[[candidateLN]]',
                '[[candidateCompanyName]]',
                '[[candidateEmail]]',
                '[[candidatePhone]]',
                '[[CandidateAddress]]',
                '[[listOfDomainsGranted]]',
                '[[CardDateOfValidation]]',
                '[[CardExpirationDate]]',
            ];
            $values = [
                $currUserFname, $currUserLname, $currUserEmail, $workshop_data->workshop_name, $workshop_data->code1, $member['p']['fullname'],
                // $member['v']['fullname'],
                $member['v']['email'], $member['p']['email'], $getOrgDetail->name_org
                , isset($WorkshopSignatory['signatory_fname']) ? $WorkshopSignatory['signatory_fname'] : '',
                isset($WorkshopSignatory['signatory_lname']) ? $WorkshopSignatory['signatory_lname'] : '',
                isset($WorkshopSignatory['signatory_possition']) ? $WorkshopSignatory['signatory_possition'] : '',
                isset($WorkshopSignatory['signatory_email']) ? $WorkshopSignatory['signatory_email'] : '',
                isset($WorkshopSignatory['signatory_phone']) ? $WorkshopSignatory['signatory_phone'] : '',
                isset($WorkshopSignatory['signatory_mobile']) ? $WorkshopSignatory['signatory_mobile'] : '',
                isset($user['fname']) ? $user['fname'] : '',
                isset($user['lname']) ? $user['lname'] : '',
                isset($user->userSkillCompany->text_input) ? $user->userSkillCompany->text_input : '',
                isset($user['email']) ? $user['email'] : '',
                isset($user['phone']) ? $user['phone'] : '',
                isset($user['address']) ? $user['address'] : '',
                isset($domain) ? $domain : '',
                isset($date['deliverydate']) ? $date['deliverydate'] : '',
                isset($date['expdeliverydate']) ? $date['expdeliverydate'] : '',
            ];
            
            $subject = (str_replace($keywords, $values, $settings->email_subject));
            return ['subject' => $subject];
        }
        
        public function copyStep()
        {
            try {
                DB::connection('tenant')->beginTransaction();
                $data = $this->stepService->copysteps(26);
                DB::connection('tenant')->commit();
                if ($data) {
                    return response()->json(['status' => TRUE], 200);
                } else {
                    return response()->json(['status' => FALSE], 200);
                }
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function resetUserMeta($userid)
        {
            try {
                DB::connection('tenant')->beginTransaction();
                $data = $this->stepService->resetUserMeta($userid);
                DB::connection('tenant')->commit();
                if ($data) {
                    return response()->json(['status' => TRUE], 200);
                } else {
                    return response()->json(['status' => FALSE], 200);
                }
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function checkValidationOfCertificationOrMemo($candidateId, $cardCount)
        {
            try {
                if ($cardCount < 0) {
                    $cardCount = 0;
                }
                $data = $this->stepService->checkValidationOfCertificationOrMemo($candidateId, $cardCount);
                if ($data['status']) {
                    return response()->json(['status' => TRUE, 'data' => $data['stepName']], 200);
                } else {
                    return response()->json(['status' => FALSE, 'data' => $data['stepName']], 200);
                }
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
    }
