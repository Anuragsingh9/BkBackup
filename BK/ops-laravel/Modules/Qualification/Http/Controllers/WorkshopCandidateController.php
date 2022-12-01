<?php
    
    namespace Modules\Qualification\Http\Controllers;
    
    use App\User;
    use App\Workshop;
    use App\WorkshopMeta;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Routing\Controller;
    use Illuminate\Support\Facades\Auth;
    use Modules\Qualification\Entities\CandidateCard;
    use Modules\Qualification\Entities\ReferrerField;
    use Modules\Qualification\Entities\ReviewStep;
    use Modules\Qualification\Entities\ReviewStepField;
    use Modules\Qualification\Entities\Step;
    use Modules\Qualification\Services\RegistrationService;
    use Validator;
    use Image;
    use DB;

    /**
     * Class WorkshopCandidateController
     * @package Modules\Qualification\Http\Controllers
     */
    class WorkshopCandidateController extends Controller
    {
        /**
         * WorkshopCandidateController constructor.
         */
        public function __construct()
        {
            $this->registrationService = RegistrationService::getInstance();
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);
        }
    
        /**
         * @param $type
         * @return \Illuminate\Http\JsonResponse
         */
        public function getWorkshopCandidate($type)
        {
            try {
                $validator = Validator::make(['type' => $type, 'role' => Auth::user()->role], [
                    'type' => 'required|numeric',
                    'role' => 'required|in:M1,M0',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                
                $wData = WorkshopMeta::with('workshop:id,code1')->with(['user' => function ($a) {
                    $a->select('id', 'fname', 'lname', 'email', 'role', 'setting', 'role_commision', 'industry_id');
                }])/*->with('user.expertReview')*/
                ->get(['id', 'role', 'user_id', 'workshop_id']);
                
                $users = User::addSelect(DB::raw('created_at as original_date'), 'id', 'fname', 'lname', 'created_at')->withCount('userCards')->with(['userSkillCompany' => function ($a) {
                    $a->select('text_input', 'field_id');
                }, 'userCards'                                                                                                                                           => function ($q) use ($type) {
                    $q->select('id', 'card_instance', 'created_at', 'date_of_validation', 'user_id', 'is_archived', 'review_done');
                }])->whereHas('userInfo', function ($b) use ($type) {
                    $b->where('is_final_save', $type);
                })->whereIn('id', $wData->pluck('user_id'))->where('sub_role', 'C1')->get();
                if (Auth::user()->role == 'M1' || Auth::user()->role == 'M0') {
                    $isAdmin = 1;
                    $superAdmin = 1;
                    $expert = 0;
                } else {
                    $isAdmin = $wData->whereIn('role', [1, 2])->where('user_id', Auth::user()->id)->count();
                    $expert = ($isAdmin) ? 0 : 1;
                }
//this is done as dan wants thing just not so
                
                if ($type == 0) {
                    
                    $users->map(function ($item, $key) use (&$users, $isAdmin, $wData) {
                        $createdAt = Carbon::today()->format('Y');
                        $cards = $item->load('userCards');
                        $carCount = $cards->userCards->count();
                        if ($carCount > 0) {
                            foreach ($item->userCards as $k => $user_card) {
                                
                                $item->userCards[$k]['date_of_validation'] = Carbon::parse($user_card['updated_at'])->format('Y-m-d');
                            }
                        }
                        $checkWorkshop = collect($wData)->where('user_id', $item->id);
                        if ($checkWorkshop->count() > 0) {
                            $workshopId = collect($wData)->where('user_id', $item->id)->first()->workshop_id;
                            $code1=(isset(collect($wData)->where('user_id', $item->id)->first()->workshop->code1)?collect($wData)->where('user_id', $item->id)->first()->workshop->code1:NULL);
                        }
                        $item->workshop_id=isset($workshopId)?$workshopId:0;
                        $item->code1=isset($code1)?$code1:0;
                    });
                    $users = collect(array_values($users->toArray()));
                    
                }
                elseif($type == 1){
                    $users->map(function ($item, $key) use (&$users, $isAdmin, $wData) {
                        $checkWorkshop = collect($wData)->where('user_id', $item->id);
                        if ($checkWorkshop->count() > 0) {
                            $workshopId = collect($wData)->where('user_id', $item->id)->first()->workshop_id;
                            $code1=(isset(collect($wData)->where('user_id', $item->id)->first()->workshop->code1)?collect($wData)->where('user_id', $item->id)->first()->workshop->code1:NULL);
                        }
                        $item->workshop_id=isset($workshopId)?$workshopId:0;
                        $item->code1=isset($code1)?$code1:0;
                    });
                }
                elseif ($type == 2) {
                    $steps = Step::get(['id']);
                    $stepCount = $steps->count() - config('constants.stepCount');
                    $done = 0;
                    $users->map(function ($item, $key) use ($wData, $stepCount, &$done, &$users, $isAdmin, &$expert) {
                        $createdAt = Carbon::today()->format('Y');
                        $cards = $item->load('userCards');
                        $carCount = $cards->userCards->count();
                        $checkWorkshop = collect($wData)->where('user_id', $item->id);
                        if ($checkWorkshop->count() > 0) {
                            $workshopId = collect($wData)->where('user_id', $item->id)->first()->workshop_id;
                            $code1=(isset(collect($wData)->where('user_id', $item->id)->first()->workshop->code1)?collect($wData)->where('user_id', $item->id)->first()->workshop->code1:NULL);
                        }
                        $item->workshop_id=isset($workshopId)?$workshopId:0;
                        $item->code1=isset($code1)?$code1:0;
                        if (($carCount <= 0) || (($carCount) % 4 == 0) || $isAdmin) {
                            
                            if ($carCount > 0) {
                                //to get max count array value
                                $carCountKey = $carCount - 1;
                                if (isset($item->userCards[$carCountKey])) {
                                    $item->done = empty($item->userCards[$carCountKey]->review_done) ? 0 : $item->userCards[$carCountKey]->review_done;
                                    $year = Carbon::parse($item->userCards[$carCountKey]->date_of_validation)->addYear(1)->format('Y');
                                    $createdAt = $year;
                                }
                            } else {
                                $item->done = 0;
                            }
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
                            }
                            
                            $wData->where('workshop_id',$item->workshop_id)->whereIn('role', [0, 1, 2])->unique('user_id')->whereIn('role', [0, 1, 2])->unique('user_id')->map(function ($item1, $key1) use ($stepCount, $item, &$done, $createdAt, $carCount, $isAdmin, $expert, &$users, $key) {
                                
                                $review = $item1->load(['user.expertReview' => function ($query) use ($createdAt, $item, $carCount, $isAdmin, $expert, $users, $key) {
                                    $query->select('id', 'saved_for', 'user_id', 'opinion_by_user', 'opinion', 'step_id', 'for_card_instance')
                                        ->where('for_card_instance', ($isAdmin && (($carCount + 1 < 5))) ? 1 : ($carCount + 1))->where('user_id', $item->id);
                                }]);
                                
                                if (isset($review->user->expertReview) && count($review->user->expertReview) > 0) {
                                    $stepCount = Step::orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->with(['domainCheckbox' => function ($b) {
                                        $b->select('skills.id', 'skill_tab_id', 'name', 'short_name', 'is_valid', 'is_mandatory', 'skill_format_id', 'is_unique', 'is_conditional', 'is_qualifying', 'tooltip_en', 'tooltip_fr');
                                    }])->where(function ($query) use ($item) {
                                        $query->whereHas('domainCheckbox.userSkill', function ($q) use ($item) {
                                            $q->where('field_id', ($item->id))->where('type', 'candidate')->where('checkbox_input', 1);
                                        })->orWhere('is_final_step', 0);
                                    })->count();
                                    if ($review->user->expertReview->where('user_id', $item->id)->count() == $stepCount - config('constants.stepCount')) {
                                        if (($expert)) {
                                            if ($review->user->id == Auth::user()->id) {
                                                $users = $users->reject(function ($value, $key) use ($item) {
                                                    return $value['id'] == $item->id;
                                                });
                                            }
                                            // dump($users);
                                        }
                                        $done = $done + 1;
                                    }
                                }
                            });
                            //apply when +9
//                            if (((($carCount - 1) % 4 == 0))) {
//                                $item->done = $done;
//                            }
                            if ($item->done == 0) {
                                $item->done = $done;
                            }
                            
                            $item->memberCount = $wData->where('workshop_id',$item->workshop_id)->filter(function ($value, $key) {
                                return $value->role != 4;
                            })->unique('user_id')->count();
                            
                            $done = 0;
                        } else {
                            $users->forget($key);
                        }
                        $checkWorkshop = collect($wData)->where('user_id', $item->id);
                        if ($checkWorkshop->count() > 0) {
                            $workshopId = collect($wData)->where('user_id', $item->id)->first()->workshop_id;
    
                            $code1=(isset(collect($wData)->where('user_id', $item->id)->first()->workshop->code1)?collect($wData)->where('user_id', $item->id)->first()->workshop->code1:NULL);
                        }
                        $item->workshop_id=isset($workshopId)?$workshopId:0;
                        $item->code1=isset($code1)?$code1:0;
                    });
                    $users = collect(array_values($users->toArray()));
                    
                    
                }
                elseif ($type == 3) {
                    $cardUser = CandidateCard::where(['is_archived' => 0])->get()->pluck('user_id');
                    $userN = User::addSelect(DB::raw('created_at as original_date'), 'id', 'fname', 'lname', 'created_at')->with(['userSkillCompany' => function ($a) {
                        $a->select('text_input', 'field_id');
                    }, 'userInfo'                           => function ($q) use ($type) {
                        $q->where('is_final_save', $type);
                    }, 'userCards'                          => function ($b) {
                        $b->where('is_archived', 0);
                    }])->withCount('userCards')->whereIn('id', $cardUser)->where('sub_role', 'C1')->get();
                    
                } elseif ($type == 4) {
                    $cardUser = CandidateCard::where(['is_archived' => 1])->get()->pluck('user_id');
                    
                    $userN = User::addSelect(DB::raw('created_at as original_date'), 'id', 'fname', 'lname', 'created_at')->with(['userSkillCompany' => function ($a) {
                        $a->select('text_input', 'field_id');
                    }, 'userCards'                          => function ($b) {
                        $b->where('is_archived', 1);
                    }])->withCount('userCards')->whereIn('id', $cardUser)->where('sub_role', 'C1')->get();
                }
                elseif($type==5){
                    $users->map(function ($item, $key) use (&$users, $isAdmin, $wData) {
                        $checkWorkshop = collect($wData)->where('user_id', $item->id);
                        if ($checkWorkshop->count() > 0) {
                            $workshopId = collect($wData)->where('user_id', $item->id)->first()->workshop_id;
                            $code1=(isset(collect($wData)->where('user_id', $item->id)->first()->workshop->code1)?collect($wData)->where('user_id', $item->id)->first()->workshop->code1:NULL);
                        }
                        $item->workshop_id=isset($workshopId)?$workshopId:0;
                        $item->code1=isset($code1)?$code1:0;
                    });
                }
                
                if (isset($userN)) {
//                $merged0 = $users->merge($userN);
                    $finalUser = [];
                    $merged0 = collect(array_merge($users->toArray(), $userN->toArray()))->unique('id')->filter();
                    
                    $merged0->map(function ($item, $key) use (&$finalUser, $type,$wData) {
                      
                        $checkWorkshop = collect($wData)->where('user_id', $item['id']);
                        if ($checkWorkshop->count() > 0) {
                            $workshopId = collect($wData)->where('user_id', $item['id'])->first()->workshop_id;
    
                            $code1=(isset(collect($wData)->where('user_id',  $item['id'])->first()->workshop->code1)?collect($wData)->where('user_id',  $item['id'])->first()->workshop->code1:NULL);
                        }
                        $item['workshop_id']=isset($workshopId)?$workshopId:0;
                        $item['code1']=isset($code1)?$code1:0;
                        
                        if ($item['user_cards_count'] != 0) {
                            foreach ($item['user_cards'] as $k => $user_card) {
                                if ($type == 4) {
                                    if ($user_card['is_archived'] == 1) {
                                        $item['user_cards_count'] = $user_card['card_instance'];
                                        $item['original_date'] = $user_card['date_of_validation'];
                                        $finalUser[] = $item;
                                    }
                                }
                                elseif ($type == 3) {
                                    if ($user_card['is_archived'] == 0) {
                                        $item['user_cards_count'] = $user_card['card_instance'];
                                        $item['original_date'] = $user_card['date_of_validation'];
                                        $finalUser[] = $item;
                                    }
                                }
                            }
                        }
                    });
                    $merged = collect($finalUser)->filter();
                    
                }
//dd();
                if (!$users) {
                    return Response()->json(['status' => FALSE, 'msg' => 'No Data Found', 'data' => []], 200);
                }
                return response()->json(['status' => TRUE, 'msg' => '', 'data' => isset($merged) ? $merged : $users], 200);
            } catch (\Exception $e) {
                dd($e);
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
    
        /**
         * @return \Illuminate\Http\JsonResponse
         */
        public function getCount()
        {
            try {
                $validator = Validator::make(['role' => Auth::user()->role], [
                    'role' => 'required|in:M1,M0',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                
                $wData = WorkshopMeta::with(['user' => function ($a) {
                    $a->select('id', 'fname', 'lname', 'email', 'role', 'setting', 'role_commision', 'industry_id');
                }])->with('user.expertReview')->get(['id', 'role', 'user_id', 'workshop_id']);
                
                $users = User::with(['userSkillCompany' => function ($a) {
                    $a->select('text_input', 'field_id');
                }, 'userInfo'                           => function ($q) {
                    $q->whereIn('is_final_save', [0, 1, 2, 3, 5]);
                }])->withCount('userCards')->whereHas('userInfo', function ($b) {
                    $b->whereIn('is_final_save', [0, 1, 2, 3, 5]);
                })->whereIn('id', $wData->pluck('user_id'))->where('sub_role', 'C1')->get(['id', 'fname', 'lname', 'created_at']);
                if (Auth::user()->role == 'M1' || Auth::user()->role == 'M0') {
                    $isAdmin = 1;
                    $expert = 0;
                } else {
                    $isAdmin = $wData->whereIn('role', [1, 2])->where('user_id', Auth::user()->id)->count();
                    $expert = ($isAdmin) ? 0 : 1;
                }
                
                $finalV = 0;
                $finaV = 0;
                $pre = 0;
                $cardRejected = 0;
                $ini = 0;
                $cardValid = 0;
                $cardValidU = [];
                
                $users->map(function ($item, $key) use (&$ini, &$pre, &$finalV, &$cardValid, &$cardValidU, $isAdmin, $wData, $expert, &$users, &$finaV, &$cardRejected) {
                    $cards = $item->load('userCards');
                    $carCount = $cards->userCards->count();
                    if ($item->userInfo->is_final_save == 0 /*&& $item->user_cards_count == 0*/) {
                        $ini = $ini + 1;
                    } elseif ($item->userInfo->is_final_save == 1 /*&& ($item->user_cards_count + 1) % 4 == 0*/) {
                        $pre = $pre + 1;
                    } elseif ($item->userInfo->is_final_save == 2) {
                        if (($item->user_cards_count <= 0) || (($item->user_cards_count) % 4 == 0) || $isAdmin) {
                            //==================
                            if (($expert)) {
                                $finalV = $finalV + 1;
                                $wData->whereIn('role', [0, 1, 2])->unique('user_id')->map(function ($item1, $key1) use ($item, &$done, $carCount, $isAdmin, $expert, &$users, $key, &$finalV, &$finaV, &$cardRejected) {
                                    $review = $item1->load(['user.expertReview' => function ($query) use ($item, $carCount, $isAdmin, $expert, $users, $key) {
                                        $query->select('id', 'saved_for', 'user_id', 'opinion_by_user', 'opinion', 'step_id', 'for_card_instance')
                                            ->where('for_card_instance', ($isAdmin && (($carCount + 1 < 5))) ? 1 : ($carCount + 1))->where('user_id', $item->id);
                                    }]);
                                    if (isset($review->user->expertReview)) {
                                        $stepCount = Step::orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->with(['domainCheckbox' => function ($b) {
                                            $b->select('skills.id', 'skill_tab_id', 'name', 'short_name', 'is_valid', 'is_mandatory', 'skill_format_id', 'is_unique', 'is_conditional', 'is_qualifying', 'tooltip_en', 'tooltip_fr');
                                        }])->where(function ($query) use ($item) {
                                            $query->whereHas('domainCheckbox.userSkill', function ($q) use ($item) {
                                                $q->where('field_id', ($item->id))->where('type', 'candidate')->where('checkbox_input', 1);
                                            })->orWhere('is_final_step', 0);
                                        })->count();
                                        
                                        if (($review->user->expertReview->where('user_id', $item->id)->count() == $stepCount - config('constants.stepCount')) && $review->user->expertReview->where('user_id', $item->id)->count() > 0) {
                                            if ($users->firstWhere('id', $item->id)) {
                                                if ($review->user->id == Auth::user()->id) {
                                                    $finalV = $finalV - 1;
                                                    // dump($finaV,$item->id,'in');
                                                }
                                            }
                                        }
                                    }
                                });
                                //====================
                                //  dump($finalV,$item->id);
                            } else {
                                $finalV = $finalV + 1;
                            }
                        }
                        
                    } elseif ($item->userInfo->is_final_save == 3) {
                        if ($item->user_cards_count != 0) {
                            $cardValid = $cardValid + 1;
                        }
                        $cardValidU[] = $item->userInfo->user_id;
                    } elseif ($item->userInfo->is_final_save == 5) {
                        $cardRejected = $cardRejected + 1;
                        
                    }
                });
                //  $finalV=$finalV-$finaV;
                $archiveCard = CandidateCard::where(['is_archived' => 1])/*->groupBy('user_id')*/
                ->count();
//            $cardUser = CandidateCard::where(['is_archived' => 0, 'workshop_id' => $wid])->whereIn('user_id', $cardValidU)->count();
                $cardUser = CandidateCard::where(['is_archived' => 0])->count();
                //  dd($cardValid, $cardUser, $cardValidU);
                if (!$users) {
                    return Response()->json(['status' => FALSE, 'msg' => 'No Data Found', 'data' => []], 200);
                }
                return response()->json(['status' => TRUE, 'msg' => '', 'data' => ['1' => $ini, '2' => $pre, '3' => $finalV, '4' => (/*$cardValid +*/
                $cardUser), '5'                                                        => $archiveCard, '6' => $cardRejected]], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
    }

