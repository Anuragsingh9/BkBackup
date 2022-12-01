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
    use Illuminate\Support\Facades\Storage;
    use Modules\Qualification\Entities\CandidateCard;
    use Modules\Qualification\Entities\ReferrerField;
    use Modules\Qualification\Entities\ReviewStep;
    use Modules\Qualification\Entities\ReviewStepField;
    use Modules\Qualification\Entities\Step;
    use Modules\Qualification\Services\RegistrationService;
    use Validator;
    use Image;
    use DB;

    class CandidateController extends Controller
    {
        private $fontPath, $imagePath;

        public function __construct()
        {
            $this->registrationService = RegistrationService::getInstance();
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);
            $this->core = app(\App\Http\Controllers\CoreController::class);
            $this->fontPath = public_path() . 'public/fonts/';
            $this->imagePath = public_path() . 'public/qualification/';
        }


        public function getWorkshopCandidate($wid, $type)
        {
            try {
                $validator = Validator::make(['wid' => $wid, 'type' => $type], [
                    'wid'  => 'required|exists:tenant.workshops,id',
                    'type' => 'required|numeric',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                $wData = WorkshopMeta::
                where(['workshop_id' => $wid/*, 'role' => 0*/])->with(['user' => function ($a) {
                    $a->select('id', 'fname', 'lname', 'email', 'role', 'setting', 'role_commision', 'industry_id');
                }])/*->with('user.expertReview')*/
                ->get(['id', 'role', 'user_id', 'workshop_id']);

                $users = User::addSelect(DB::raw('created_at as original_date'), 'id', 'fname', 'lname', 'created_at')->withCount('userCards')->with(['userSkillCompany' => function ($a) {
                    $a->select('text_input', 'field_id');
                }, /*'userInfo'=> function ($q) use ($type) {
                    $q->where('is_final_save', $type);
                },*/ 'userCards'                                                                                                                      => function ($q) use ($type) {
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
                            $code1 = (isset(collect($wData)->where('user_id', $item->id)->first()->workshop->code1) ? collect($wData)->where('user_id', $item->id)->first()->workshop->code1 : NULL);
                        }
                        $item->workshop_id = isset($workshopId) ? $workshopId : 0;
                        $item->code1 = isset($code1) ? $code1 : 0;
                    });
                    $users = collect(array_values($users->toArray()));

                } elseif ($type == 1) {
                    $users->map(function ($item, $key) use (&$users, $isAdmin, $wData) {
                        $checkWorkshop = collect($wData)->where('user_id', $item->id);
                        if ($checkWorkshop->count() > 0) {
                            $workshopId = collect($wData)->where('user_id', $item->id)->first()->workshop_id;
                            $code1 = (isset(collect($wData)->where('user_id', $item->id)->first()->workshop->code1) ? collect($wData)->where('user_id', $item->id)->first()->workshop->code1 : NULL);
                        }
                        $item->workshop_id = isset($workshopId) ? $workshopId : 0;
                        $item->code1 = isset($code1) ? $code1 : 0;
                    });
                } elseif ($type == 2) {
                    $steps = Step::get(['id']);
                    $stepCount = $steps->count() - config('constants.stepCount');
                    $done = 0;
                    $users->map(function ($item, $key) use ($wData, $stepCount, &$done, &$users, $isAdmin, &$expert) {
                        $checkWorkshop = collect($wData)->where('user_id', $item->id);
                        if ($checkWorkshop->count() > 0) {
                            $workshopId = collect($wData)->where('user_id', $item->id)->first()->workshop_id;
                            $code1 = (isset(collect($wData)->where('user_id', $item->id)->first()->workshop->code1) ? collect($wData)->where('user_id', $item->id)->first()->workshop->code1 : NULL);
                        }
                        $item->workshop_id = isset($workshopId) ? $workshopId : 0;
                        $item->code1 = isset($code1) ? $code1 : 0;
                        $createdAt = Carbon::today()->format('Y');
                        $cards = $item->load('userCards');
                        $carCount = $cards->userCards->count();
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

                            $wData->whereIn('role', [0, 1, 2])->unique('user_id')->map(function ($item1, $key1) use ($stepCount, $item, &$done, $createdAt, $carCount, $isAdmin, $expert, &$users, $key, $forIns, $fourRenewal) {

                                $review = $item1->load(['user.expertReview' => function ($query) use ($createdAt, $item, $carCount, $isAdmin, $expert, $users, $key, $forIns, $fourRenewal) {
                                    $query->select('id', 'saved_for', 'user_id', 'opinion_by_user', 'opinion', 'step_id', 'for_card_instance')
                                        ->where('for_card_instance', ($isAdmin && (($carCount + 1 < 5))) ? 1 : ($forIns))->where('user_id', $item->id);
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
                                } else {
                                    if (in_array($forIns, $fourRenewal))
                                        $item->done = $done;
                                }
                            });
                            //apply when +9
//                            if (((($carCount - 1) % 4 == 0))) {
//                                $item->done = $done;
//                            }
                            if ($item->done == 0) {
                                $item->done = $done;
                            }

                            $item->memberCount = $wData->filter(function ($value, $key) {
                                return $value->role != 4;
                            })->unique('user_id')->count();
                            $done = 0;
                        } else {
                            $users->forget($key);
                        }
                    });
                    $users = collect(array_values($users->toArray()));


                } elseif ($type == 3) {
                    $cardUser = CandidateCard::where(['is_archived' => 0, 'workshop_id' => $wid])->get()->pluck('user_id');
                    $userN = User::with(['userSkillCompany' => function ($a) {
                        $a->select('text_input', 'field_id');
                    }, 'userInfo'                           => function ($q) use ($type) {
                        $q->where('is_final_save', $type);
                    }, 'userCards'                          => function ($b) {
                        $b->where('is_archived', 0);
                    }])->withCount('userCards')->whereIn('id', $cardUser)->where('sub_role', 'C1')->addSelect(DB::raw('created_at as original_date'))->get(['id', 'fname', 'lname', 'created_at']);
                } elseif ($type == 4) {
                    $cardUser = CandidateCard::where(['is_archived' => 1, 'workshop_id' => $wid])->get()->pluck('user_id');

                    $userN = User::with(['userSkillCompany' => function ($a) {
                        $a->select('text_input', 'field_id');
                    }, 'userCards'                          => function ($b) {
                        $b->where('is_archived', 1);
                    }])->withCount('userCards')->whereIn('id', $cardUser)->where('sub_role', 'C1')->addSelect(DB::raw('created_at as original_date'))->get(['id', 'fname', 'lname', 'created_at']);
                } elseif ($type == 5) {
                    $users->map(function ($item, $key) use (&$users, $isAdmin, $wData) {
                        $checkWorkshop = collect($wData)->where('user_id', $item->id);
                        if ($checkWorkshop->count() > 0) {
                            $workshopId = collect($wData)->where('user_id', $item->id)->first()->workshop_id;
                            $code1 = (isset(collect($wData)->where('user_id', $item->id)->first()->workshop->code1) ? collect($wData)->where('user_id', $item->id)->first()->workshop->code1 : NULL);
                        }
                        $item->workshop_id = isset($workshopId) ? $workshopId : 0;
                        $item->code1 = isset($code1) ? $code1 : 0;
                    });
                }
                if (isset($userN)) {

//                $merged0 = $users->merge($userN);
                    $finalUser = [];
                    $merged0 = collect(array_merge($users->toArray(), $userN->toArray()))->unique('id')->filter();

                    $merged0->map(function ($item, $key) use (&$finalUser, $type, $wData) {
                        $checkWorkshop = collect($wData)->where('user_id', $item['id']);
                        if ($checkWorkshop->count() > 0) {
                            $workshopId = collect($wData)->where('user_id', $item['id'])->first()->workshop_id;

                            $code1 = (isset(collect($wData)->where('user_id', $item['id'])->first()->workshop->code1) ? collect($wData)->where('user_id', $item['id'])->first()->workshop->code1 : NULL);
                        }
                        $item['workshop_id'] = isset($workshopId) ? $workshopId : 0;
                        $item['code1'] = isset($code1) ? $code1 : 0;
                        if ($item['user_cards_count'] != 0) {

                            foreach ($item['user_cards'] as $k => $user_card) {
                                if ($type == 4) {
                                    if ($user_card['is_archived'] == 1) {
                                        $item['user_cards_count'] = $user_card['card_instance'];
                                        $item['original_date'] = $user_card['date_of_validation'];
                                        $finalUser[] = $item;
                                    }
                                } elseif ($type == 3) {
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
                //dd($e);
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        public function getCandidateSteps($userId, $status = 0, $forCard = 0)
        {
            try {
                $createdAt = Carbon::today()->format('Y');
                $user = User::with('userCards', 'userMeta')->with(['userSkillCompany' => function ($a) {
                    $a->select('text_input', 'field_id');
                }, 'userInfo:id,user_id,is_final_save'])->whereId($userId)->where('sub_role', 'C1')->addSelect(DB::raw('created_at as original_date,id, fname,lname,email'))->first(['id', 'fname', 'lname', 'email', 'created_at']);

                $carCount = count($user->userCards);
                $increment = 0;
                $fourRenewal = [5, 9, 13, 17, 21, 25];
                $renewal = range(1, 25);
                if ($status < 3) {
                    $carCount = count($user->userCards);
                    if ($carCount > 0) {
                        //to get max count array value
                        $carCountKey = $carCount - 1;
                        if (isset($user->userCards[$carCountKey])) {
                            $year = Carbon::parse($user->userCards[$carCountKey]->date_of_validation)->addYear(1)->format('Y');
                            $createdAt = $year;
                        }

                    }
                    $increment = $increment + 1;
//                    if ((($carCount) % 4 == 0) && ($carCount > 0)){
//                        $key = array_search(($carCount + 1), $fourRenewal);
//                    $forIns = $fourRenewal[$key];
//                } else {
//                        $key = array_search(($carCount + 1), $renewal);
//                        $forIns = 1;
//                        foreach ($fourRenewal as $k => $val) {
//                            if ($renewal[$key] <= $val) {
//                                if ($renewal[$key] >= 5)
//                                    $forIns = $fourRenewal[$k];
//                                else
//                                    $forIns = 1;
//                            }
//                        }
//                    }
                    if ((($carCount) % 4 == 0) && (($carCount > 0))) {
                        $key = array_search(($carCount + 1), $fourRenewal);
                        $forIns = $fourRenewal[$key];
                    } else {
                        $key = array_search(($carCount), $renewal);
                        $forIns = 1;
                        foreach ($fourRenewal as $k => $val) {

                            if ($renewal[$key] <= $val) {
                                if ($renewal[$key] >= 5) {
                                    // @todo need to check that the $renewal[$key] in %4
                                    if ($k != 0) {
                                        if ($renewal[$key] > 8) {
                                            $forIns = $fourRenewal[$k];
                                        } else {
                                            $forIns = $fourRenewal[$k - 1];
                                        }

                                    } else
                                        $forIns = $fourRenewal[$k];
                                    break;

                                } else {
                                    $forIns = 1;
                                    break;
                                }
                            }
                        }
                    }
                } else {
                    $carCount = $forCard;
                    $increment = 0;
                    if ((($carCount) % 4 == 0) && (($carCount > 0))) {
                        $key = array_search(($carCount + 1), $fourRenewal);
                        $forIns = $fourRenewal[$key];
                    } else {
                        $key = array_search(($carCount), $renewal);
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

//                    if ((($carCount) > 0) && ($carCount % 4 == 0)) {
//                        $key = array_search(($carCount), $fourRenewal);
//                        $forIns = $fourRenewal[$key];
//                    } else {
//                        $key = array_search(($carCount + 1), $renewal);
//                        $forIns = 1;
//                        foreach ($fourRenewal as $k => $val) {
//                            if ($renewal[$key] <= $val) {
//                                if ($renewal[$key] >= 5)
//                                    $forIns = $fourRenewal[$k];
//                                else
//                                    $forIns = 1;
//                            }
//                        }
//                    }

                }

                if (!in_array(Auth::user()->role, ['M1', 'M0'])) {
                    $role = getUserWorkshopRole(Auth::user(), $user->userMeta[0]->workshop_id);
                    if ($role === FALSE)
                        $role = 0;
                } else {
                    $role = 1;
                }

                //feth the list of steps ->date_of_validation
                $steps = Step::orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->with([
                    'stepReview'       => function ($a) use ($userId, $createdAt, $role, $carCount, $increment) {
                        $a->where('user_id', $userId)->whereIn('opinion_by', [$role, ($role == 1) ? '' : 0]);
                        /*->whereYear('saved_for', $createdAt)*/
                        $a->where('for_card_instance', ($carCount != 0) ? ($carCount + 1) : ($carCount + 1));
                    },
                    'domainCheckbox'   => function ($b) {
                        $b->select('skills.id', 'skill_tab_id', 'name', 'short_name', 'is_valid', 'is_mandatory', 'skill_format_id', 'is_unique', 'is_conditional', 'is_qualifying', 'tooltip_en', 'tooltip_fr');
                    },
                    'stepReviewYellow' => function ($a) use ($userId, $createdAt, $role, $carCount, $increment, $forIns) {
                        $a->where('user_id', $userId)->whereIn('opinion_by', [($role == 1) ? 0 : 0])/*->whereYear('saved_for', $createdAt)->where('for_card_instance',($carCount+1))*/
                        ->where('for_card_instance', $forIns);;
                    }, 'stepReviewRed' => function ($a) use ($userId, $createdAt, $role, $carCount, $increment, $forIns) {
                        $a->where('user_id', $userId)->whereIn('opinion_by', [($role == 1) ? 0 : 0])/*->whereYear('saved_for', $createdAt)->where('for_card_instance',($carCount+1))*/
                        ->where('for_card_instance', $forIns);;
                    }, 'stepReviewYellow.user', 'stepReviewRed.user'])->withCount(
                    ['stepReview as green'    => function ($query) use ($userId, $createdAt, $role, $carCount, $increment, $forIns) {
                        $query->where('opinion', 0)->whereIn('opinion_by', [($role == 1) ? 0 : 0])
                            ->where('user_id', $userId)/*->whereYear('saved_for', $createdAt)->where('for_card_instance',($carCount+1))*/
                            ->where('for_card_instance', $forIns);;
                    }, 'stepReview as yellow' => function ($query) use ($userId, $createdAt, $role, $carCount, $increment, $forIns) {
                        $query->where('opinion', 1)->whereIn('opinion_by', [($role == 1) ? 0 : 0])
                            ->where('user_id', $userId, $createdAt)/*->whereYear('saved_for', $createdAt)->where('for_card_instance',($carCount+1))*/
                            ->where('for_card_instance', $forIns);;
                    }, 'stepReview as red'    => function ($query) use ($userId, $createdAt, $role, $carCount, $increment, $forIns) {

                        $query->where('opinion', 2)->whereIn('opinion_by', [($role == 1) ? 0 : 0])
                            ->where('user_id', $userId, $createdAt)/*->whereYear('saved_for', $createdAt)->where('for_card_instance',($carCount+1))*/
                            ->where('for_card_instance', $forIns);
                    }])->where(function ($query) use ($userId) {
                    $query->whereHas('domainCheckbox.userSkill', function ($q) use ($userId) {
                        $q->where('field_id', ($userId))->where('type', 'candidate')->where('checkbox_input', 1);
                    })->orWhere('is_final_step', 0);
                })->get(['id', 'name', 'description', 'is_conditional', 'is_final_step', 'button_text', 'sort_order']);
                //dd($steps);
                if (!$steps) {
                    return response()->json(['status' => FALSE, 'msg' => 'no data found', 'data' => []], 200);
                } else {
                    return response()->json(['status' => TRUE, 'data' => ['steps' => $steps, 'userInfo' => $user]], 200);
                }

            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        public function getCandidateStepFields($id, $userId, $wid = 0, $status = 0, $forCard = 0)
        {
            try {
                //fetch the list of steps
                $validator = Validator::make(['wid' => $wid, 'candidate_id' => $userId, 'id' => $id], [
                    'wid'          => 'required|exists:tenant.workshops,id',
                    'candidate_id' => 'required|exists:tenant.users,id',
                    'id'           => 'required|exists:tenant.qualification_steps,id',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }

                if ($wid !== 0) {
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
                    $workshop = WorkshopMeta::where(['workshop_id' => $wid, 'user_id' => Auth::user()->id])->whereIn('role', [1, 2])->count();
                    if (($workshop > 0) || (in_array(Auth::user()->role, ['M1', 'M0']))) {

                        $steps = Step::where('id', $id)->with(['fields' => function ($b) {
                            $b->select('skills.id', 'skill_tab_id', 'name', 'short_name', 'is_valid', 'is_mandatory', 'skill_format_id', 'is_unique', 'is_conditional', 'is_qualifying');
                        }, 'fields.userSkill'                           => function ($q) use ($userId) {
                            $date = Carbon::today()->format('Y-m-d');
                            $q->where('field_id', ($userId))->where('type', 'candidate');

                        }, 'fields.UserHaveManySkills'                  => function ($q) use ($userId) {
                            $q->where('field_id', ($userId))->where('type', 'candidate');
                        }, 'fields.skillImages', 'fields.skillSelect', 'fields.skillCheckBox', 'fields.skillMeta', 'fields.skillCheckBoxAcceptance'])->with(['fields.fieldReview' => function ($b) use ($userId, $id, $createdAt, $carCount, $workshop, $forCard, $status) {
//                            if ($status < 3) {
//                                if ($forCard != 0) {
//                                    $forCard = $forCard + 1;
//                                }
//                            } else {
//                                if ($forCard != 1)
//                                    $forCard = $forCard + 1;
//                            }
                            $forCard = $forCard + 1;
//                            dd($forCard);
//                        dd($userId,$id,($status < 3) ? ($forCard + 1) :$forCard);
                            $b->where('user_id', ($userId))->where('step_id', $id)->where('opinion_by_user', Auth::user()->id)/*->whereYear('saved_for', $createdAt)*/
                            ->where('opinion_by', 1)
                                ->where('for_card_instance', ($forCard == 0) ? ($forCard + 1) : $forCard);
                        }])->first(['id', 'name', 'description', 'is_conditional', 'is_final_step', 'button_text', 'sort_order']);
                    } else {
                        $steps = Step::where('id', $id)->with(['fields' => function ($b) {
                            $b->select('skills.id', 'skill_tab_id', 'name', 'short_name', 'is_valid', 'is_mandatory', 'skill_format_id', 'is_unique', 'is_conditional', 'is_qualifying');
                        }, 'fields.UserHaveManySkills'                  => function ($q) use ($userId) {
                            $q->where('field_id', ($userId))->where('type', 'candidate');
                        }, 'fields.userSkill'                           => function ($q) use ($userId) {
                            $q->where('field_id', ($userId))->where('type', 'candidate');
                        }, 'fields.skillImages', 'fields.skillSelect', 'fields.skillCheckBox', 'fields.skillMeta', 'fields.skillCheckBoxAcceptance'])->with(['fields.fieldReview' => function ($b) use ($userId, $id, $createdAt, $carCount) {
                            $b->where('user_id', ($userId))->where('step_id', $id)->where('opinion_by_user', Auth::user()->id)/*->whereYear('saved_for', $createdAt)*/
                            ->where('for_card_instance', ($carCount + 1));
                        }])->first(['id', 'name', 'description', 'is_conditional', 'is_final_step', 'button_text', 'sort_order']);
                    }
                }
                if (!$steps) {
                    return response()->json(['status' => FALSE, 'msg' => 'no data found', 'data' => []], 200);
                } else {
                    $steps['user_card_count'] = $carCount;
                    return response()->json(['status' => TRUE, 'data' => isset($steps->fields) ? $steps : []], 200);
                }

            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        public function saveFieldsReview(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'step_id'      => 'required|exists:tenant.qualification_steps,id',
                    'field_id'     => 'required|exists:tenant.skills,id',
                    'opinion'      => 'required|in:0,1,2',
                    'candidate_id' => 'required|exists:tenant.users,id',
                    'opinion_by'   => 'required|in:0,1,2',
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
//checking and adding card entery and savedFor
                $candidate = User::with('userCards')->find($request->candidate_id, ['id']);
                $saveFor = Carbon::today();
                $carCount = count($candidate->userCards);
                if ($carCount > 0) {
                    //to get max count array value
                    $carCountKey = $carCount - 1;
                    if (isset($candidate->userCards[$carCountKey])) {
                        $year = Carbon::parse($candidate->userCards[$carCountKey]->date_of_validation)->addYear(1);
                        $saveFor = $year;
                    }
                }

                //checking EntryExist or not
                if ($request->opinion_by == 1) {
                    $where = ['step_id' => $request->step_id, 'field_id' => $request->field_id, 'user_id' => $request->candidate_id, 'opinion_by' => $request->opinion_by, 'opinion_by_user' => Auth::user()->id];
                } else {
                    $where = ['step_id' => $request->step_id, 'field_id' => $request->field_id, 'user_id' => $request->candidate_id, 'opinion_by' => $request->opinion_by, 'opinion_by_user' => Auth::user()->id];
                }

                $reviewFieldCheck = ReviewStepField::where($where)->where('for_card_instance', ($request->cardCount + 1))/*->where(DB::raw('YEAR(saved_for)'), $saveFor->format('Y'))*/
                ->first();

                if (isset($reviewFieldCheck->id)) {
                    $reviewFieldUp = ReviewStepField::where('id', $reviewFieldCheck->id)->update(
                        ['step_id' => $request->step_id, 'field_id' => $request->field_id, 'user_id' => $request->candidate_id, 'opinion_by' => $request->opinion_by, 'opinion' => $request->opinion, 'opinion_by_user' => Auth::user()->id]
                    );
                    $reviewField = $reviewFieldCheck;
                } else {
                    $reviewField = ReviewStepField::create(
                        ['step_id' => $request->step_id, 'field_id' => $request->field_id, 'user_id' => $request->candidate_id, 'opinion_by' => $request->opinion_by, 'opinion' => $request->opinion, 'opinion_by_user' => Auth::user()->id, 'saved_for' => $saveFor->format('Y-m-d h:i:s'), 'for_card_instance' => ($request->cardCount + 1)]
                    );
                }

                if (!$reviewField) {
                    return response()->json(['status' => FALSE, 'msg' => 'No Data Found', 'data' => []], 200);
                } else {
                    return response()->json(['status' => TRUE, 'data' => $reviewField], 200);
                }

            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        public function saveStepsReview(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'opinion'      => 'required|in:0,1,2',
                    'opinion_text' => 'required_if:opinion,1|required_if:opinion,2|max:255',
                    'candidate_id' => 'required|exists:tenant.users,id',
                    'opinion_by'   => 'required|in:0,1,2',
                    'step_id'      => 'required|exists:tenant.qualification_steps,id',
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                //checking and adding card entry and savedFor
                $candidate = User::with('userCards')->find($request->candidate_id, ['id']);
                $saveFor = Carbon::today();
                $carCount = count($candidate->userCards);
                if ($carCount > 0) {
                    //to get max count array value
                    $carCountKey = $carCount - 1;
                    if (isset($candidate->userCards[$carCountKey])) {
                        $year = Carbon::parse($candidate->userCards[$carCountKey]->date_of_validation)->addYear(1);
                        $saveFor = $year;
                    }
                }
                //checking EntryExist or not
                $reviewStep = $this->updateOrAddStepReview($request, $carCount, $saveFor);

                if (!$reviewStep) {
                    return response()->json(['status' => FALSE, 'msg' => 'No Data Found', 'data' => []], 200);
                } else {
                    return response()->json(['status' => TRUE, 'data' => $reviewStep], 200);
                }

            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }


        public function getCandidateFinalData($workshopId, $userId, $cardCount)
        {

            try {
                $user = User::with(['userSkillCompany'              => function ($a) {
                    $a->select('text_input', 'field_id');
                }, 'userInfo:id,user_id,is_final_save', 'userCards' => function ($b) use ($cardCount) {
                    $b->where('card_instance', $cardCount + 1);
                }])->withCount('userCards')->whereId($userId)->where('sub_role', 'C1')->first(['id', 'fname', 'lname', 'created_at', 'postal']);

                $workshop = Workshop::withoutGlobalScopes()->find($workshopId, ['id', 'setting']);

                $field = Step::where('sort_order', 1)->first(['id']);
                /* $getAdminStepFields = Step::where('id', $field->id)->with(['fields' => function ($b) {
                     $b->select('skills.id', 'skills.skill_tab_id', 'skills.name', 'skills.short_name', 'skills.is_valid', 'skills.is_mandatory', 'skills.skill_format_id', 'skills.is_unique', 'skills.sort_order', 'skills.is_conditional', 'skills.is_qualifying');
                 }, 'fields.skillFormat' => function ($a) {
                     $a->select('id', 'name_en', 'name_fr');
                 }, 'fields.userSkill' => function ($q) use ($userId) {
                     $q->where('field_id', $userId)->where('type', 'candidate');
                 }, 'fields.skillCheckBox', 'fields.skillMeta'])->with(['fields.domainCheckbox.domainSkill.userSkill'=> function ($a) use ($userId) {
                     $a->where(function ($c) use ($userId) {
                         $c->where('field_id', $userId);
                         $c->where('type', 'candidate');
                         $c->where(function ($d) {
                             $d->orWhere('mandatory_checked_by', 1);
                             $d->orWhere('checkbox_input', 1);
                         });
                     });
                 }])->first(['id', 'name', 'description', 'is_conditional', 'is_final_step', 'button_text', 'sort_order']);*/
                $domain = Step::orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->where('is_final_step', 1)->with(['domainCheckboxSingle.domainStep.stepReview' => function ($a) use ($userId, $user, $cardCount) {
                    $a->where(function ($c) use ($userId, $user, $cardCount) {
                        $c->where('opinion_by', 1);
                        $c->where('user_id', $userId);
                        $c->where('opinion', 0);
                        $c->where('for_card_instance', ($cardCount + 1));
                    });
                }])->whereHas('domainCheckboxSingle.domainStep.stepReview', function ($a) use ($userId, $user, $cardCount) {
                    $a->where(function ($c) use ($userId, $user, $cardCount) {
                        $c->where('opinion_by', 1);
                        $c->where('user_id', $userId);
                        $c->where('opinion', 0);
                        $c->where('for_card_instance', ($cardCount + 1));
                    });
                })->get();
                $field->fields = $domain;
//          dd();
                return response()->json(['status' => TRUE, 'data' => ['user' => $user, 'workshop' => $workshop, 'domain' => $field]], 200);

            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        public function generateImageGD($workshopId, $userId, $carCount = 0)
        {

            // Set the content-type
            header('Content-Type: image/png');

            // Create the image
            $im = imagecreatefrompng('http://sharabh.ooionline.com\public\qualification\images\card-icon.png');

            // Create some colors
            $white = imagecolorallocate($im, 255, 255, 255);
            $grey = imagecolorallocate($im, 128, 128, 128);
            $black = imagecolorallocate($im, 0, 0, 0);
            imagefilledrectangle($im, 0, 0, 399, 29, $white);

            // The text to draw
            $text = 'Testing...';
            // Replace path by your own font path

            $font = dirname('C:') . '\Windows\Fonts\arial.ttf';
            // Add some shadow to the text
            //        imagettftext($im, 20, 0, 11, 21, $grey, $font, $text);

            // Add the text
            imagettftext($im, 20, 0, 210, 20, $black, $font, $text);

            // Using imagepng() results in clearer text compared with imagejpeg()
            dd(imagepng($im));

            //=======================================================================================//
            //        $pdfName = 'Certification-image.pdf';
            //        $pdfUrl = public_path('public' . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . $pdfName);
            //        $url = url('/') . "/qualification/certification-image/pdf/" . $workshopId . '/' . $userId;
            ////        dd($url);
            //        $command = 'wkhtmltopdf -T 10mm -B 10mm -L 0 -R 0 --orientation portrait --page-size A4 --encoding "UTF-8"' . " $url $pdfUrl 2>&1";
            ////         dd($command);
            //        shell_exec($command);
            //        return redirect('public/pdf/Certification-image.pdf');
        }

        public function certificationImageGD($workshopId, $userId, $carCount = 0)
        {
            try {
                $field = $this->registrationService->getStepZeroSkillNew($userId, $carCount);
                $domain = $field['domain'];
                $field = $field['getAdminStepFields'];

                //$field = $field1->fields;
                $otherfield = $this->registrationService->getStepfields($userId, $carCount);
                $date = $this->registrationService->getDeliveryDate($userId, $carCount);

                $user = User::with('userSkillCompany')->find($userId);
                if (strlen($user->postal) < 5) {
                    $user->postal = str_pad($user->postal, 5, '0', STR_PAD_LEFT);
                }
                $workshop = Workshop::where('id', $workshopId)->withoutGlobalScopes()->first();
                if ($workshop != NULL) {
                    if ($workshop->setting != NULL) {
                        $setting = $workshop->setting;
                        $ext = pathinfo(env('AWS_PATH') . $setting['web']['header_logo'], PATHINFO_EXTENSION);
                        if (strtolower($ext) == 'jpeg' || strtolower($ext) == 'jpg') {
                            $workshop->workshop_logo = imagecreatefromjpeg(env('AWS_PATH') . $setting['web']['header_logo']);
                        } elseif (strtolower($ext) == 'png') {
                            $workshop->workshop_logo = imagecreatefrompng(env('AWS_PATH') . $setting['web']['header_logo']);
                        } elseif (strtolower($ext) == 'gif') {
                            $workshop->workshop_logo = imagecreatefromgif(env('AWS_PATH') . $setting['web']['header_logo']);
                        }
                        $workShopLogo = $workshop->workshop_logo;
                        $workShopLogo = $this->PIPHP_ImageResize($workShopLogo, 96, 46);
                    } else {
                        $settings_data = getSettingData('pdf_graphic');
                        $settings_data->header_logo = $this->core->getS3Parameter($settings_data->header_logo, 2);
                        //sharabh\/uploads\/kNWRre9TPYJL8rjoc56fPuywjRYDsV6H4INv4FH0.png
                        //https://s3-eu-west-2.amazonaws.com/ooionline.com/cartetppro/uploads/IqXMjqSlxNb8QP8I6ZMVbPjwhSiIkrgAxenCFODS.gif
                        //cartetppro/uploads/F42gXDHUs614Wjte9GQvvSzQgwkPfCkgDIqvhIix.jpeg
                        $updatedData['header_logo'] = $settings_data->header_logo;
                        $ext = pathinfo($settings_data->header_logo, PATHINFO_EXTENSION);
                        if (strtolower($ext) == 'jpeg' || strtolower($ext) == 'jpg') {
                            $workshop->workshop_logo = imagecreatefromjpeg(env('AWS_PATH') . $settings_data->header_logo);
                        } elseif (strtolower($ext) == 'png') {
                            $workshop->workshop_logo = imagecreatefrompng(env('AWS_PATH') . $settings_data->header_logo);
                        } elseif (strtolower($ext) == 'gif') {
                            $workshop->workshop_logo = imagecreatefromgif(env('AWS_PATH') . $settings_data->header_logo);
                        }
                        $workShopLogo = $workshop->workshop_logo;
                        $workShopLogo = $this->PIPHP_ImageResize($workShopLogo, 96, 46);
                    }
                }

                $font = $this->fontPath . 'OpenSans-Regular.ttf';
                // Create image instances
                $dest = imagecreatefrompng($this->imagePath . 'images/cartetpro-card_new.png');
                // Create some colors
                $white = imagecolorallocate($dest, 255, 255, 255);
                $grey = imagecolorallocate($dest, 128, 128, 128);
                $black = imagecolorallocate($dest, 0, 0, 0);
                $nameColor = 2247054;
                $companyName = getQualificationCompanyName($user);
                $_font = $this->fontPath . 'OpenSans-Semibold.ttf';
                $postalFont = $this->fontPath . 'QanelasSemiBold.ttf';
                imagettftext($dest, 14, 0, 35, 35, $nameColor, $postalFont, $companyName);//
                imagettftext($dest, 11, 0, 35, 53, $nameColor, $postalFont, $user->postal);
                if (!empty($domain) && count($domain) == 1) {
                    $pos = 170;
                } else {
                    $pos = 170;
                }

                $arrowIcon = imagecreatefrompng($this->imagePath . 'images/corner-arrow.png');
                $fontDomain = $this->fontPath . 'OpenSans-Semibold.ttf';
                foreach ($domain as $k => $item) {
                    // First we create our bounding box for the first text
                    $itemVal = rtrim($item->name);
                    $box = imageftbbox(8, 0, $fontDomain, $itemVal);
                    $len = (imagesx($dest)) - $box[4] - 34;
                    imagettftext($dest, 8, 0, $len, $pos, $white, $fontDomain, $itemVal);
                    imagecopymerge($dest, $arrowIcon, 412, ($pos - 6), 0, 0, 18, 7, 99);
                    $pos += 18;
                }
                //   exit;
                // header('Content-Type: image/png');
                if (isset($workshop->workshop_logo))
                    $src = $workShopLogo;

                $src0 = imagecreatefrompng($this->imagePath . 'images/cartetpro-artisan.png');
                //  $_src = imagecreatefrompng('G:\work\ops-laravel/public/qualification/ffb-logo.png');
                // Copy and merge
                //here we merge companyLogo with card image
                if (isset($src) && !empty($src)) {
                    imagecopymerge($dest, $src, 296, 19, 0, 0, 96, 46, 99);
                }
                //here we merge cartetLogo with card image
                imagecopymerge($dest, $src0, 35, 131, 0, 0, 66, 100, 99);

                $dateFont = $this->fontPath . 'OpenSans-Bold.ttf';
                //getting validation and expiry dates
                $validationDate = isset($date['deliverydate_orig']) ? ($date['deliverydate_orig']) : (\Carbon\Carbon::now()->format('Y'));
                $expiryDate = isset($date['expdeliverydate_orig']) ? ($date['expdeliverydate_orig']) : (\Carbon\Carbon::now()->addYear(1)->format('Y'));
                // The Validation and expiry dates to draw
                imagettftext($dest, 8, 0, 55, 248, $white, $dateFont, $validationDate);
                imagettftext($dest, 8, 0, 55, 260, $white, $dateFont, $expiryDate);

                $pdfName = 'Certification-image' . time() . '.png';
                $pdfUrl = public_path('pdf' . DIRECTORY_SEPARATOR . $pdfName);
                // Transparent Background
                $r = rand(0, 255);
                $g = rand(0, 255);
                $b = rand(0, 255);
                $alphacolor = imagecolorallocatealpha($dest, $r, $g, $b, 127);
                imagealphablending($dest, FALSE);
                imagesavealpha($dest, TRUE);
                // Replace path by your own font path
                header('Content-Type: image/png');
//            dd(imagepng($dest));
                (imagepng($dest, $pdfUrl, 9));
                return response()->download($pdfUrl);
            } catch (\Exception $e) {
                dd($e);
                abort(404, 'Something Went Wrong Please Try again!');
            }
        }

        /*
         * old working
         */
        public function generateImage($workshopId, $userId, $carCount = 0)
        {


            $pdfName = 'Certification-image' . time() . '.png';
            $pdfUrl = public_path('pdf' . DIRECTORY_SEPARATOR . $pdfName);
            $url = url('/') . "/qualification/certification-image/pdf/" . $workshopId . '/' . $userId . '/' . $carCount;
//                    dd($url);

            $command = 'xvfb-run /home/wkhtmltoimage  --width 428 --height 270 --transparent --format png   --encoding "UTF-8"' . " $url $pdfUrl 2>&1";
            //         dd($command);
            (shell_exec($command));
            return response()->download($pdfUrl);
            return redirect('pdf/Certification-image.png');
        }

        public function certificationImage($workshopId, $userId, $carCount = 0)
        {

            $field = $this->registrationService->getStepZeroSkillNew($userId, $carCount);
            $domain = $field['domain'];
            $field = $field['getAdminStepFields'];

            //$field = $field1->fields;
            $otherfield = $this->registrationService->getStepfields($userId, $carCount);
            //dd($field);
            $date = $this->registrationService->getDeliveryDate($userId, $carCount);
            // dd($date);
            $user = User::with('userSkillCompany')->find($userId);
            if (strlen($user->postal) < 5) {
                $user->postal = str_pad($user->postal, 5, '0', STR_PAD_LEFT);
            }
            // $zipCode = substr(str_replace(' ', '', $user->postal), 0, 2);
            // $workshop = Workshop::where('code1', $zipCode)->where('is_qualification_workshop', '!=', 0)->withoutGlobalScopes()->first();
            $workshop = Workshop::where('id', $workshopId)->withoutGlobalScopes()->first();
            if ($workshop != NULL) {
                if ($workshop->setting != NULL) {
                    $setting = $workshop->setting;
                    $workshop->workshop_logo = env('AWS_PATH') . $setting['web']['header_logo'];

                } else {
                    $settings_data = getSettingData('pdf_graphic');
                    $settings_data->header_logo = $this->core->getS3Parameter($settings_data->header_logo, 2);
                    $updatedData['header_logo'] = $settings_data->header_logo;
                    $workshop->workshop_logo = $settings_data->header_logo;
                }
            }
            // dd($workshop);
            return view('qualification::certification_image')->with(compact('field', 'user', 'workshop', 'otherfield', 'date', 'domain'));
            // return view('qualification::certification');

        }

        public function getCount($wid)
        {
            try {
                $validator = Validator::make(['wid' => $wid], [
                    'wid' => 'required|exists:tenant.workshops,id',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }

                $wData = WorkshopMeta::where(['workshop_id' => $wid/*, 'role' => 0*/])->with(['user' => function ($a) {
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
                $archiveCard = CandidateCard::where(['is_archived' => 1, 'workshop_id' => $wid])/*->groupBy('user_id')*/
                ->count();

//            $cardUser = CandidateCard::where(['is_archived' => 0, 'workshop_id' => $wid])->whereIn('user_id', $cardValidU)->count();
                $cardUser = CandidateCard::where(['is_archived' => 0, 'workshop_id' => $wid])->count();
//              dd($cardValid, $cardUser, $cardValidU);
                if (!$users) {
                    return Response()->json(['status' => FALSE, 'msg' => 'No Data Found', 'data' => []], 200);
                }
                return response()->json(['status' => TRUE, 'msg' => '', 'data' => ['1' => $ini, '2' => $pre, '3' => $finalV, '4' => (/*$cardValid +*/
                $cardUser), '5'                                                        => $archiveCard, '6' => $cardRejected]], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        public function updateOrAddStepReview($request, $carCount, $saveFor)
        {
            if ((isset($request->reedit) && $request->reedit) && ($request->opinion_by == 1)) {
                $reviewStep = ReviewStep::where('id', $request->reedit)->update(
                    ['user_id'      => $request->candidate_id, 'opinion_by' => $request->opinion_by, 'opinion' => $request->opinion,
                     'opinion_text' => (isset($request->opinion_text) ? $request->opinion_text : NULL), 'opinion_by_user' => Auth::user()->id, 'step_id' => $request->step_id]
                );
                $reviewStep = ReviewStep::where('id', $request->reedit)->first();
            } elseif
            (isset($request->card_count) && isset($request->reedit)
            ) {
                $reviewStep = ReviewStep::create(
                    ['user_id' => $request->candidate_id, 'opinion_by' => $request->opinion_by, 'opinion' => $request->opinion, 'opinion_text' => (isset($request->opinion_text) ? $request->opinion_text : NULL), 'opinion_by_user' => Auth::user()->id, 'step_id' => $request->step_id, 'saved_for' => $saveFor->format('Y-m-d h:i:s'), 'for_card_instance' => ($request->card_count == 0) ? ($request->card_count + 1) : $request->card_count]
                );

            } else {
                if ($request->opinion_by == 1) {
                    $where = ['user_id' => $request->candidate_id, 'opinion_by' => $request->opinion_by, 'step_id' => $request->step_id];
                } else {
                    $where = ['user_id' => $request->candidate_id, 'opinion_by' => $request->opinion_by, 'opinion_by_user' => Auth::user()->id, 'step_id' => $request->step_id];
                }
                $reviewStepCheck = ReviewStep::where($where)/*->where(DB::raw('YEAR(saved_for)'), $saveFor->format('Y'))*/
                ->where('for_card_instance', ($carCount + 1))->first(['id']);

                if (isset($reviewStepCheck->id)) {
                    $reviewStep = ReviewStep::where('id', $reviewStepCheck->id)->update(
                        ['user_id'      => $request->candidate_id, 'opinion_by' => $request->opinion_by, 'opinion' => $request->opinion,
                         'opinion_text' => (isset($request->opinion_text) ? $request->opinion_text : NULL), 'opinion_by_user' => Auth::user()->id, 'step_id' => $request->step_id]
                    );
                    $reviewStep = ReviewStep::where('id', $reviewStepCheck->id)->first();
                } else {
                    $reviewStep = ReviewStep::create(
                        ['user_id' => $request->candidate_id, 'opinion_by' => $request->opinion_by, 'opinion' => $request->opinion, 'opinion_text' => (isset($request->opinion_text) ? $request->opinion_text : NULL), 'opinion_by_user' => Auth::user()->id, 'step_id' => $request->step_id, 'saved_for' => $saveFor->format('Y-m-d h:i:s'), 'for_card_instance' => ($carCount + 1)]
                    );
                }
            }
            return $reviewStep;
        }

        function PIPHP_ImageResize($image, $w, $h)
        {
            $oldw = imagesx($image);
            $oldh = imagesy($image);
            $temp = imagecreatetruecolor($w, $h);
            imagecopyresampled($temp, $image, 0, 0, 0, 0, $w, $h, $oldw, $oldh);
            return $temp;
        }

    }

