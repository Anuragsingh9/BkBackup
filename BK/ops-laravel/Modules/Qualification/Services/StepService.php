<?php
    
    /**
     * Created by PhpStorm.
     * User: Sourabh Pancharia
     * Date: 6/25/2019
     * Time: 04:14 PM
     */
    
    namespace Modules\Qualification\Services;
    
    use App\Model\Skill;
    use App\Model\SkillTabs;
    use App\Model\UserMeta;
    use App\Workshop;
    
    use App\User;
    use App\WorkshopMeta;
    use Illuminate\Http\Request;
    use Auth;
    use DB;
    use Carbon\Carbon;
    use Modules\Qualification\Entities\CandidateCard;
    use Modules\Qualification\Entities\DomainCheckbox;
    use Modules\Qualification\Entities\ReviewStep;
    use Modules\Qualification\Entities\ReviewStepField;
    use Modules\Qualification\Entities\Step;
    use Modules\Qualification\Entities\CandidateField;
    use Modules\Qualification\Entities\Field;
    
    class StepService
    {
        /**
         * SuperAdminSingleton constructor.
         */
        private $contactServices;
        
        
        /**
         * Make instance of SuperAdmin singleton class
         * @return SuperAdmin|null
         */
        public static function getInstance()
        {
            static $instance = NULL;
            if (NULL === $instance) {
                $instance = new static();
            }
            return $instance;
        }
        
        
        public function createCard(array $data)
        {
            
            try {
                
                $cardInstance = ($this->getInstanceNo($data['user_id']) == 0) ? 1 : ($this->getInstanceNo($data['user_id']) + 1);
                $cardNo = $this->getCardNo($data['user_id'], $data['wid']);
                $userMeta = UserMeta::where('user_id', $data['user_id'])->first();
                //as date submitted as 0000:00:00 like so we use this condition
                $dateOfVal = $this->dateOfValidation($userMeta, $data['user_id']);
                $cardReview = $this->reviewDone($data['user_id'], $data['wid'], $cardInstance);
                $card = CandidateCard::create([
                    'user_id'            => $data['user_id'],
                    'card_instance'      => $cardInstance,
                    'card_no'            => $cardNo,
                    'workshop_id'        => $data['wid'],
                    'review_done'        => $cardReview,
                    'date_of_validation' => $dateOfVal,
                    'is_archived'        => 0,
                    'final_by'           => (isset($userMeta->final_by)) ? $userMeta->final_by : Auth::user()->id,
                ]);
                return $card;
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function getInstanceNo(int $userId)
        {
            
            return CandidateCard::where('user_id', $userId)->count();
        }
        
        public function getCardNo(int $userId, int $wid)
        {
            $workshp = Workshop::withoutGlobalScopes()->find($wid, ['id', 'code1']);
            $incrementNo = getCardIncrementNumber($wid);
            //        $incrementNo = 1;
            $stepNo = generateRandomValue(2);
//        $stepNo = $this->getStepGranted($userId);
            
            $incrementNo = str_pad($incrementNo, 3, '0', STR_PAD_LEFT);
            
            return $cardNo = ($workshp->code1 . date('y') . $incrementNo . $stepNo);
        }
        
        public function getStepGranted(int $userId)
        {
            $step = Step::orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->get(['id']);
            
            $createdAt = Carbon::today()->format('Y');
            $user = User::with('userCards')->find($userId, ['id']);
            $carCount = count($user->userCards);
            if ($carCount > 0) {
                //to get max count array value
                $carCountKey = $carCount - 1;
                if (isset($user->userCards[$carCountKey])) {
                    $year = Carbon::parse($user->userCards[$carCountKey]->date_of_validation)->addYear(1)->format('Y');
                    $createdAt = $year;
                }
            }
            
            $stepReview = ReviewStep::where(['user_id' => $userId, 'opinion_by' => 1, 'opinion_by_user' => \Auth::user()->id])->whereYear('saved_for', $createdAt)->orderBy('step_id')->get(['id', 'opinion', 'step_id', 'user_id']);
            $series = '';
            
            $step->slice(1)->map(function ($item, $key) use (&$series, $stepReview, $userId) {
                
                $check = $stepReview->where('step_id', $item->id)->first();
                if (isset($check->step_id) && $check->opinion == 0) {
                    $series = $series . '1';
                } else {
                    $series = $series . '0';
                }
            });
            return $series;
        }
        
        //RESET user meta table
        public function resetUserMeta($userId)
        {
            $step = Step::where('sort_order', 1)->first(['id']);
            // return $series;
            return UserMeta::where('user_id', $userId)
                ->update(['is_final_save'   => 0,
                          'saved_at'        => NULL,
                          'current_step_id' => (isset($step->id) ? $step->id : 1)]);
        }
        
        public function copysteps($userId)
        {
            $step = Step::all(['id'])->pluck('id');
            $field = Field::whereIn('step_id', $step)->get(['id', 'step_id', 'field_id']);
            foreach ($field as $key => $item) {
                $Candidatefield = CandidateField::updateOrCreate(['qualification_field_id' => $item->id, 'user_id' => $userId], ['qualification_field_id' => $item->id, 'user_id' => $userId]);
            }
            if ($Candidatefield) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
        
        public function copyPersonalChecking($userId, $card)
        {
            $reviews = ReviewStepField::where(['for_card_instance' => $card->card_instance, 'user_id' => $userId])->get();
            $ins = [];
            foreach ($reviews as $key => $item) {
                $item->for_card_instance = ($card->card_instance + 1);
                $item->created_at = (Carbon::now()->format('Y-m-d'));
                $item->updated_at = NULL;
                $arr = $item->toArray();
                unset($arr['id']);
                $ins[] = $arr;
            }
            if (count($ins) > 0) {
                ReviewStepField::insert($ins);
                return TRUE;
            } else {
                return FALSE;
            }
        }
        
        public function checkValidationOfCertificationOrMemo($candidateId, $cardCount)
        {
            //this is for additional working which to check for all mandatory fields
            $getSteps = Step::orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->where('is_final_step', 0)->orWhere(function ($b) use ($candidateId, $cardCount) {
                $b->
                whereHas('domainCheckboxSingle.domainSkill.userSkill', function ($a) use ($candidateId, $cardCount) {
                    $a->where(['field_id' => $candidateId, 'type' => 'candidate'])->where('checkbox_input', 1);
                });
            })->get(['id', 'name', 'description', 'is_conditional', 'is_final_step', 'button_text', 'sort_order']);
            
            $steps = $getSteps->pluck('id');
            $field = Field::with(['skill' => function ($a) {
                $a->where('is_qualifying', '!=', 0)->whereNotIn('skill_format_id',[1,2,4,5,15,16,20,14,18]);
            }, 'skill.userSkill'          => function ($q) use ($candidateId) {
                $q->where('field_id', ($candidateId))->where('type', 'candidate');
            }])->whereIn('step_id', $steps)->orderBy('step_id','ASC')->get(['id', 'step_id', 'field_id']);
            $stepName = $getSteps->pluck('name', 'id');
            //->where('for_card_instance', $forIns)
            // $candidateId=3932;
            $fourRenewal = [5, 9, 13, 17, 21, 25];
            $renewal = range(1, 25);
            if ((($cardCount) % 4 == 0) && (($cardCount > 0))) {
                $key = array_search(($cardCount + 1), $fourRenewal);
                $forIns = $fourRenewal[$key];
            } else {
                $key = array_search(($cardCount + 1), $renewal);
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
            }
            $contained = [];
            $notContained = [];
            $stepIds = [];
            $value = [];
            foreach ($field as $k => $item) {
                if ($item->skill != NULL) {
                    //here we need to check if it is qualifing 3 fields validation or 1,2
                    if ($item->skill->is_qualifying == 1) {
                        if ($item->skill->userSkill != NULL) {
                            $isValid1 = ($item->skill->userSkill->for_card_instance == 1) ? 1 : 0;
                            if ($isValid1) {
                                if (isset($contained[$item->step_id][$item->skill->skill_format_id])) {
                                    $contained[$item->step_id][$item->skill->skill_format_id] = $contained[$item->step_id][$item->skill->skill_format_id] + 1;
                                } else {
                                    $contained[$item->step_id][$item->skill->skill_format_id] = 1;
                                }
                            }
                            else {
                                if (isset($notContained[$item->step_id][$item->skill->skill_format_id])) {
                                    $notContained[$item->step_id][$item->skill->skill_format_id] = $notContained[$item->step_id][$item->skill->skill_format_id] + 1;
                                } else {
                                    $notContained[$item->step_id][$item->skill->skill_format_id] = 1;
                                }
                                if (!in_array($stepName[$item->step_id], $stepIds)) {
                                    $stepIds[$item->step_id] = $stepName[$item->step_id];
                                    $value[$item->step_id] = $item->step_id;
                                }
                            }
                            
                        } else {
                            if (isset($notContained[$item->step_id][$item->skill->skill_format_id])) {
                                $notContained[$item->step_id][$item->skill->skill_format_id] = $notContained[$item->step_id][$item->skill->skill_format_id] + 1;
                            } else {
                                $notContained[$item->step_id][$item->skill->skill_format_id] = 1;
                            }
                            if (!in_array($stepName[$item->step_id], $stepIds)) {
                                $stepIds[$item->step_id] = $stepName[$item->step_id];
                                $value[$item->step_id] = $item->step_id;
                            }
                        }
                    } elseif ($item->skill->is_qualifying == 2) {
                        if ($item->skill->userSkill != NULL) {
                            $isValid2 = ($item->skill->userSkill->for_card_instance == ($cardCount + 1)) ? 1 : 0;
                            if ($isValid2) {
                                if (isset($contained[$item->step_id][$item->skill->skill_format_id])) {
                                    $contained[$item->step_id][$item->skill->skill_format_id] = $contained[$item->step_id][$item->skill->skill_format_id] + 1;
                                } else {
                                    $contained[$item->step_id][$item->skill->skill_format_id] = 1;
                                }
                            }
                            else {
                                if (isset($notContained[$item->step_id][$item->skill->skill_format_id])) {
                                    $notContained[$item->step_id][$item->skill->skill_format_id] = $notContained[$item->step_id][$item->skill->skill_format_id] + 1;
                                } else {
                                    $notContained[$item->step_id][$item->skill->skill_format_id] = 1;
                                }
                                if (!in_array($stepName[$item->step_id], $stepIds)) {
                                    $stepIds[$item->step_id] = $stepName[$item->step_id];
                                    $value[$item->step_id] = $item->step_id;
                                }
                            }
                            
                        }
                        else {
                            if (isset($notContained[$item->step_id][$item->skill->skill_format_id])) {
                                $notContained[$item->step_id][$item->skill->skill_format_id] = $notContained[$item->step_id][$item->skill->skill_format_id] + 1;
                            } else {
                                $notContained[$item->step_id][$item->skill->skill_format_id] = 1;
                            }
                            if (!in_array($stepName[$item->step_id], $stepIds)) {
                                $stepIds[$item->step_id] = $stepName[$item->step_id];
                                $value[$item->step_id] = $item->step_id;
                            }
                        }
                    } elseif ($item->skill->is_qualifying == 3) {
                        if ($item->skill->userSkill != NULL) {
                            $isValid2 = ($item->skill->userSkill->for_card_instance ==$forIns);
                            if ($isValid2) {
                                if (isset($contained[$item->step_id][$item->skill->skill_format_id])) {
                                    $contained[$item->step_id][$item->skill->skill_format_id] = $contained[$item->step_id][$item->skill->skill_format_id] + 1;
                                } else {
                                    $contained[$item->step_id][$item->skill->skill_format_id] = 1;
                                }
                            }
                            else {
                                if (isset($notContained[$item->step_id][$item->skill->skill_format_id])) {
                                    $notContained[$item->step_id][$item->skill->skill_format_id] = $notContained[$item->step_id][$item->skill->skill_format_id] + 1;
                                } else {
                                    $notContained[$item->step_id][$item->skill->skill_format_id] = 1;
                                }
                                if (!in_array($stepName[$item->step_id], $stepIds)) {
                                    $stepIds[$item->step_id] = $stepName[$item->step_id];
                                    $value[$item->step_id] = $item->step_id;
                                }
                            }
        
                        }
                        else {
                            if (isset($notContained[$item->step_id][$item->skill->skill_format_id])) {
                                $notContained[$item->step_id][$item->skill->skill_format_id] = $notContained[$item->step_id][$item->skill->skill_format_id] + 1;
                            } else {
                                $notContained[$item->step_id][$item->skill->skill_format_id] = 1;
                            }
                            if (!in_array($stepName[$item->step_id], $stepIds)) {
                                $stepIds[$item->step_id] = $stepName[$item->step_id];
                                $value[$item->step_id] = $item->step_id;
                            }
                        }
                    }
                }
            }
        
            $res = [];
            foreach ($value as $k => $v) {
              
                if (isset($contained[$v]) && count($contained[$v]) == 0 && count($notContained[$v]) == 0) {
                    $res[] = ['status' => TRUE, 'stepName' => $stepIds[$v]];
                } else if (isset($contained[$v]) && count($contained[$v]) > 0 && isset($contained[$v][21]) && $contained[$v][21] >= 2) {
                    $res[] = ['status' => TRUE, 'stepName' => $stepIds[$v]];
                } else if (isset($contained[$v]) && count($contained[$v]) > 0 && isset($contained[$v][22])) {
                    $res[] = ['status' => TRUE, 'stepName' => $stepIds[$v]];
                } else {
                    $res[] = ['status' => FALSE, 'stepName' => $stepIds[$v]];
                }
            }
           
            $flag = TRUE;
            $stepValue = [];
            foreach ($res as $k => $item) {
                if ($item['status'] == FALSE) {
                    $flag = FALSE;
                    $stepValue[] = $item['stepName'];
                }
            }
            if ($flag) {
                return ['status' => TRUE, 'stepName' => $stepValue];
            } else {
                return ['status' => FALSE, 'stepName' => $stepValue];
            }
            /* $data = Step::orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->with('conditional:id,is_checked,conditional_checkbox_id,step_id', 'domainCheckboxSingle.domainSkill.userSkill')->whereHas('domainCheckboxSingle.domainSkill.userSkill', function ($a) use ($candidateId, $cardCount) {
              $a->where(['field_id' => $candidateId, 'type' => 'candidate'])->where('checkbox_input', 1);
          })->get(['id', 'name', 'description', 'is_conditional', 'is_final_step', 'button_text', 'sort_order']);
          //'for_card_instance'=>$cardCount+1
          $steps = $data->pluck('id');
          $field = Field::with(['skill' => function ($a) {
              $a->whereIn('skill_format_id', [21, 22]);
          }, 'skill.userSkill'          => function ($q) use ($candidateId, $forIns) {
              $q->where('field_id', ($candidateId))->where('type', 'candidate')->where('for_card_instance', $forIns);
          }])->whereIn('step_id', $steps)->get(['id', 'step_id', 'field_id']);
          $stepName = $data->pluck('name', 'id');
          
          foreach ($field as $k => $item) {
              if ($item->skill != NULL) {
                  if ($item->skill->userSkill != NULL) {
                      if (isset($contained[$item->step_id][$item->skill->skill_format_id])) {
                          $contained[$item->step_id][$item->skill->skill_format_id] = $contained[$item->step_id][$item->skill->skill_format_id] + 1;
                      } else {
                          $contained[$item->step_id][$item->skill->skill_format_id] = 1;
                      }
                  } else {
                      if (isset($notContained[$item->step_id][$item->skill->skill_format_id])) {
                          $notContained[$item->step_id][$item->skill->skill_format_id] = $notContained[$item->step_id][$item->skill->skill_format_id] + 1;
                      } else {
                          $notContained[$item->step_id][$item->skill->skill_format_id] = 1;
                      }
                      if (!in_array($stepName[$item->step_id], $stepIds)) {
                          $stepIds[$item->step_id] = $stepName[$item->step_id];
                          $value[$item->step_id] = $item->step_id;
                      }
                  }
                  
              }
          }
          // dd($notContained,$contained);
          $res = [];
          foreach ($value as $k => $v) {
              if (isset($contained[$v]) && count($contained[$v]) == 0 && count($notContained[$v]) == 0) {
                  $res[] = ['status' => TRUE, 'stepName' => $stepIds[$v]];
              } else if (isset($contained[$v]) && count($contained[$v]) > 0 && isset($contained[$v][21]) && $contained[$v][21] >= 2) {
                  $res[] = ['status' => TRUE, 'stepName' => $stepIds[$v]];
              } else if (isset($contained[$v]) && count($contained[$v]) > 0 && isset($contained[$v][22])) {
                  $res[] = ['status' => TRUE, 'stepName' => $stepIds[$v]];
              } else {
                  $res[] = ['status' => FALSE, 'stepName' => $stepIds[$v]];
              }
          }
          $flag = TRUE;
          $stepValue = [];
          foreach ($res as $k => $item) {
              if ($item['status'] == FALSE) {
                  $flag = FALSE;
                  $stepValue[] = $item['stepName'];
              }
          }*/
        }
        
        public function createInitialStepFields(int $stepId)
        {
            
            try {
                $this->createDomainCheckbox($stepId);
                $skills = Skill::where('is_valid', 1)->whereIn('skill_format_id', [21, 22])->get(['id', 'skill_format_id', 'name']);
//as we are creating the 3 default fields in Migration
                //count($skills) >= 3 && ((count($skills->where('skill_format_id', 21)->take(2))) == 2 && count($skills->where('skill_format_id', 22)->take(1)) == 1)
//                if (FALSE) {
//                    $skills->where('skill_format_id', 21)->take(2)->map(function ($item, $key) use ($stepId) {
//                        Field::create(['step_id' => $stepId, 'field_id' => $item->id]);
//                    });
//                    $skills->where('skill_format_id', 22)->take(1)->map(function ($item, $key) use ($stepId) {
//                        Field::create(['step_id' => $stepId, 'field_id' => $item->id]);
//                    });
//                    return TRUE;
//                } else {
                /* if (count($skills->where('skill_format_id', 21)->take(2)) < 2) {
                     $data = [
                         [
                             'name' => 'Attestation N°1',
                             'skill_tab_id' => $this->getCandidateTab(),
                             'short_name' => 'Attestation N°1',
                             'skill_format_id' => 21,
                             'is_unique' => 0,
                             'is_qualifying' => 3,
                             'is_conditional' => 0,
                             'target_blank' => 1,
                         ],
                         [
                             'name' => 'Attestation N°2',
                             'skill_tab_id' => $this->getCandidateTab(),
                             'short_name' => 'Attestation N°2',
                             'skill_format_id' => 21,
                             'is_unique' => 0,
                             'is_qualifying' => 3,
                             'is_conditional' => 0,
                             'target_blank' => 1,
                         ]
                     ];
                     foreach ($data as $key => $value) {
                         Skill::updateOrCreate(['name' => $value['name']], $value);
                     }
                 }
                 if (count($skills->where('skill_format_id', 22)->take(1)) < 1) {
                     $data = [
                         [
                             'name' => 'Technical memo',
                             'skill_tab_id' => $this->getCandidateTab(),
                             'short_name' => 'Technical memo',
                             'skill_format_id' => 22,
                             'is_unique' => 0,
                             'is_qualifying' => 3,
                             'is_conditional' => 0,
                             'target_blank' => 1,
                         ]
                     ];
                     foreach ($data as $key => $value) {
                         Skill::updateOrCreate(['name' => $value['name']], $value);
                     }
                 }
                 $skills = Skill::whereIn('skill_format_id', [21, 22])->get(['id', 'skill_format_id', 'name']);
                 $skills->where('skill_format_id', 21)->take(2)->map(function ($item, $key) use ($stepId) {
                     Field::create(['step_id' => $stepId, 'field_id' => $item->id]);
                 });
                 $skills->where('skill_format_id', 22)->take(1)->map(function ($item, $key) use ($stepId) {
                     Field::create(['step_id' => $stepId, 'field_id' => $item->id]);
                 });*/
                
                $data = [
                    [
                        'name'            => 'Attestation N°1',
                        'skill_tab_id'    => $this->getCandidateTab(),
                        'short_name'      => 'Attestation N°1',
                        'skill_format_id' => 21,
                        'is_unique'       => 0,
                        'is_qualifying'   => 3,
                        'is_conditional'  => 0,
                        'target_blank'    => 1,
                    ],
                    [
                        'name'            => 'Attestation N°2',
                        'skill_tab_id'    => $this->getCandidateTab(),
                        'short_name'      => 'Attestation N°2',
                        'skill_format_id' => 21,
                        'is_unique'       => 0,
                        'is_qualifying'   => 3,
                        'is_conditional'  => 0,
                        'target_blank'    => 1,
                    ],
                ];
                foreach ($data as $key => $value) {
                    $ref = Skill::create($value);
                    Field::create(['step_id' => $stepId, 'field_id' => $ref->id]);
                }
                $data1 = [
                    [
                        'name'            => 'Technical memo',
                        'skill_tab_id'    => $this->getCandidateTab(),
                        'short_name'      => 'Technical memo',
                        'skill_format_id' => 22,
                        'is_unique'       => 0,
                        'is_qualifying'   => 3,
                        'is_conditional'  => 0,
                        'target_blank'    => 1,
                    ],
                ];
                foreach ($data1 as $key => $value1) {
                    $mem = Skill::create($value1);
                    Field::create(['step_id' => $stepId, 'field_id' => $mem->id]);
                }
                return TRUE;

//                }
            } catch (\Exception $e) {
                return $e->getMessage();
                
            }
        }
        
        public function getCandidateTab()
        {
            $skillTab = SkillTabs::where('tab_type', 5)->first(['id']);
            return (isset($skillTab->id) ? $skillTab->id : 1);
        }
        
        public function createDomainCheckbox(int $stepId)
        {
            $stepData = Step::find($stepId, ['name', 'description']);
            $skillCount = Skill::count() + 1;
            $skill = Skill::create([
                'skill_tab_id'    => $this->getCandidateTab(),
                'name'            => $stepData->name,
                'short_name'      => $stepData->name,
                'description'     => $stepData->description,
                'image'           => '',
                //'is_valid' => $request->is_valid,
                'is_mandatory'    => 0,
                'skill_format_id' => 1,
                'is_unique'       => 0,
                'comment'         => '',
                'link_text'       => '',
                'comment_link'    => '',
                'sort_order'      => $skillCount,
                'is_conditional'  => 0,
                'is_qualifying'   => 1,
                'tooltip_en'      => $stepData->name,
                'tooltip_fr'      => $stepData->name
                //'comment_target_blank' => $request->comment_target_blank,
            
            ]);
            DomainCheckbox::create(['step_id' => $stepId, 'skill_id' => $skill->id]);
        }
        
        public function reviewDone(int $userId, int $wid, int $cardInstance)
        {
            $done = 0;
            $forIns = 1;
            $fourRenewal = [5, 8, 12, 16, 20, 24];
            $renewal = range(1, 25);
            $newCount = ($cardInstance == 5) ? ($cardInstance - 1) : $cardInstance;
            if ($newCount - 1 % 4 == 0 && ($cardInstance) != 0) {
                $key = array_search(($cardInstance), $fourRenewal);
                $forIns = $fourRenewal[$key];
            } else {
                $key = array_search($cardInstance, $renewal);
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
                        /*     if ($renewal[$key] >= 5)
                                 $forIns = $fourRenewal[$k];
                             else
                                 $forIns = 1;*/
                    }
                }
            }
            
            if (in_array($cardInstance, $fourRenewal) || $cardInstance == 1) {
                $stepCount = Step::where('is_final_step', 1)->orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->with(['domainCheckbox' => function ($b) {
                    $b->select('skills.id', 'skill_tab_id', 'name', 'short_name', 'is_valid', 'is_mandatory', 'skill_format_id', 'is_unique', 'is_conditional', 'is_qualifying', 'tooltip_en', 'tooltip_fr');
                }])->where(function ($query) use ($userId, $forIns) {
                    $query->whereHas('domainCheckbox.userSkill', function ($q) use ($userId, $forIns) {
                        $q->where('field_id', ($userId))->where('type', 'candidate')->where('checkbox_input', 1)->orderBy('id', 'desc')/*->where('for_card_instance', $forIns)*/
                        ;
                    })->orWhere('is_final_step', 0);
                })->count();
                
                $wData = WorkshopMeta::
                where(['workshop_id' => $wid/*, 'role' => 0*/])->with(['user' => function ($a) {
                    $a->select('id', 'fname', 'lname', 'email', 'role', 'setting', 'role_commision', 'industry_id');
                }])/*->with('user.expertReview')*/
                ->get(['id', 'role', 'user_id', 'workshop_id']);
                
                $wData->whereIn('role', [0, 1, 2])->unique('user_id')->map(function ($item1, $key1) use ($userId, $forIns, &$done, $stepCount) {
                    $review = $item1->load(['user.expertReview' => function ($query) use ($userId, $forIns, &$done, $stepCount) {
                        $query->select('id', 'saved_for', 'user_id', 'opinion_by_user', 'opinion', 'step_id', 'for_card_instance')/*->whereYear('saved_for', $createdAt)*/
                        ->where('for_card_instance', $forIns)->where('user_id', $userId);
                    }]);
                    //dump($item1->user_id);
                    if (isset($review->user->expertReview)) {
                        if (($review->user->expertReview->where('user_id', $userId)->count()/* - config('constants.stepCount')*/) == $stepCount) {
                            $done = $done + 1;
                        }
                    }
                });
            } else {
                $card = CandidateCard::where('user_id', $userId)->orderBy('id', 'desc')->first();
                $done = $card->review_done;
            }
            
            return $done;
        }
        
        public function dateOfValidation($userMeta, $userId)
        {
            $today = Carbon::today()->format('Y-m-d h:i:s');
            $dateOfVal = $today;
            $card = CandidateCard::where('user_id', $userId)->orderBy('id', 'desc')->first();
            if (isset($card->id)) {
                $first = Carbon::parse($card->date_of_validation)->addYear(1)->subDay(1);
                $second = Carbon::parse($userMeta->saved_at);
                if ($first->greaterThan($second)) {
                    $dateOfVal = $first->addDay(1)->format('Y-m-d');
                } else {
                    $dateOfVal = (isset($userMeta->saved_at) && !empty($userMeta->saved_at)) ? $userMeta->saved_at : $today;
                }
            } else {
                $dateOfVal = (isset($userMeta->saved_at) && !empty($userMeta->saved_at)) ? $userMeta->saved_at : $today;
            }
            return $dateOfVal;
        }
        
        public function getGrantedDomain($userId, $cardCount)
        {
            $domain = Step::orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->where('is_final_step', 1)->with(['domainCheckboxSingle.domainStep.stepReview' => function ($a) use ($userId, $cardCount) {
                $a->where(function ($c) use ($userId, $cardCount) {
                    $c->where('opinion_by', 1);
                    $c->where('user_id', $userId);
                    $c->where('opinion', 0);
                    $c->where('for_card_instance', ($cardCount + 1));
                });
            }])->whereHas('domainCheckboxSingle.domainStep.stepReview', function ($a) use ($userId, $cardCount) {
                $a->where(function ($c) use ($userId, $cardCount) {
                    $c->where('opinion_by', 1);
                    $c->where('user_id', $userId);
                    $c->where('opinion', 0);
                    $c->where('for_card_instance', ($cardCount + 1));
                });
            })->count();
            return $domain;
        }
    }
