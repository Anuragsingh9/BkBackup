<?php
    
    namespace App\Http\Controllers\ImproveMentFour;
    
    use App\Http\Controllers\Controller;
    use App\Model\Skill;
    use App\Model\SkillTabFormat;
    use App\Model\SkillTabs;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Validator;
    use function GuzzleHttp\json_encode;
    use stdClass;
    
    /**
     * Class SkillTabsController
     * @package App\Http\Controllers\ImproveMentFour
     */
    class SkillTabsController extends Controller
    {
        /**
         * @return \Illuminate\Http\JsonResponse
         */
        public function getSkillTypes()
        {
            try {
                $skillFormat = SkillTabFormat::whereNotIn('id',[21,22])->get();
                return response()->json([
                    'status' => TRUE,
                    'data'   => $skillFormat,
                ], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 201);
            }
        }
        
        /**
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function index(Request $request)
        {
            $type = ($request->tab_type == 'undefined') ? 0 : $request->tab_type;
            $permissions = \Auth::user()->permissions;
            $crmAdmin = (isset($permissions['crmAdmin']) && $permissions['crmAdmin'] == 1) ?? 0;
            
            try {
                if ($request->has('wid') && $request->wid) {
                    $data = SkillTabs::withCount('skills')->where(['tab_type' => $type, 'is_valid' => 1])->orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->addSelect(\DB::raw("CASE WHEN is_locked = 1 and visible IS NULL THEN '{\"crmAssistance\":0,\"crmEditor\":0,\"crmRecruitment\":0,\"workshopAdmin\":1,\"user\":1}' WHEN is_locked = 2 and visible IS NULL THEN '{\"crmAssistance\":0,\"crmEditor\":0,\"crmRecruitment\":0,\"workshopAdmin\":0,\"user\":0}' WHEN is_locked = 0 and visible IS NULL THEN '{\"crmAssistance\":0,\"crmEditor\":0,\"crmRecruitment\":0,\"workshopAdmin\":1,\"user\":1}' ELSE (select visible) END as visible"))
                        ->where(function ($q) {
                            $q->orWhere('visible->workshopAdmin', 1);
                        })
                        ->get();
                } else {
                    $data = SkillTabs::withCount('skills')->where(['tab_type' => $type, 'is_valid' => 1])->orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->addSelect(\DB::raw("CASE WHEN is_locked = 1 and visible IS NULL THEN '{\"crmAssistance\":0,\"crmEditor\":0,\"crmRecruitment\":0,\"workshopAdmin\":1,\"user\":1}' WHEN is_locked = 2 and visible IS NULL THEN '{\"crmAssistance\":0,\"crmEditor\":0,\"crmRecruitment\":0,\"workshopAdmin\":0,\"user\":0}' WHEN is_locked = 0 and visible IS NULL THEN '{\"crmAssistance\":0,\"crmEditor\":0,\"crmRecruitment\":0,\"workshopAdmin\":1,\"user\":1}' ELSE (select visible) END as visible"))
                        ->where(function ($q) use ($crmAdmin) {
                            if (Auth::user()->role != 'M1') {
                                if ((!$crmAdmin)) {
                                    if ((Auth::user()->role == 'M2')) {
                                        $q->orWhere('visible->user', 1);
                                    }
                                }
                            }
                        })->orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->get();
                }
                
                return response()->json(['status' => TRUE, 'data' => $data], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 201);
            }
        }
        
        /**
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function add(Request $request)
        {
            
            try {
                $validator = Validator::make($request->all(), [
                    'id'       => 'required',
                    'tab_type' => 'required',
                    'name'     => 'required|regex:/^[^\s]+(\s+[^\s]+)*$/',
                    'visible'  => 'required',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                
                if ($request->id > 0) {
                    $res = SkillTabs::where('id', $request->id)->update(['name' => $request->name, 'is_locked' => $request->is_locked]);
                    return response()->json(['status' => $res, 'data' => $res], 200);
                }
                //get skill tab count and add sort sorder for new skilltabs
                $count = SkillTabs::where('tab_type', $request->tab_type)->count() + 1;
                $request->merge(['sort_order' => $count]);
                $request->merge(['visible' => '{"crmAssistance":0,"crmEditor":0,"crmRecruitment":0,"workshopAdmin":0,"user":0}']);
                $request->merge(['is_locked' => 0]);
                $request->merge(['created_by' => Auth::user()->id]);
                
                $res = SkillTabs::create($request->all());
                return response()->json(['status' => ($res != NULL) ? TRUE : FALSE, 'data' => $res], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 201);
            }
        }
        
        /**
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function updateVisibleToPresence(Request $request)
        {
            // Update presence list status
            try {
                $validator = Validator::make($request->all(), [
                    'id'     => 'required',
                    'status' => 'required',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                $res = SkillTabs::where('id', '>', 0)->update(['added_to_presence' => FALSE]);
                
                $res = SkillTabs::where('id', $request->id)->update(['added_to_presence' => ($request->status == TRUE) ? TRUE : FALSE]);
                
                return response()->json(['status' => TRUE, 'data' => SkillTabs::select('added_to_presence')->find($request->id)], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 201);
            }
        }
        
        /**
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function deleteTab(Request $request)
        {
            try {
                $res = SkillTabs::where('id', $request->id)->update(['is_valid' => 0]);
                if ($res) {
                    Skill::where('skill_tab_id', $request->id)->update(['is_valid' => 0]);
                }
                return response()->json(['status' => $res, 'data' => $res], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 201);
            }
        }
        
        /**
         * @param $id
         * @param $userId
         * @return \Illuminate\Http\JsonResponse
         */
        public function getSkills($id, $userId, $type = NULL)
        {
            try {
                //->where('is_locked', 0) removed as not showing data for allowed user
                
                $res = SkillTabs::where('id', $id)->where('is_valid', 1)->with(['skills' => function ($query) {
                    $query->select('id', 'name', 'short_name', 'skill_format_id', 'skill_tab_id');
                }])->with(['skills.skillFormat' => function ($q) {
                    $q->select('id', 'name_en', 'name_fr');
                }, 'skills.skillImages', 'skills.skillSelect', 'skills.skillCheckBox', 'skills.skillMeta', 'skills.skillCheckBoxAcceptance'])->with(['skills.userSkill' => function ($e) use ($userId, $type) {
                    
                    if ((!empty($type)) && $type != 'null' && $type != 'user')
                        $e->where('field_id', ($userId))->where('type', $type);
                    else
                        $e->where('user_id', (!empty($userId) ? $userId : Auth::user()->id));
                }])->first(['id', 'name', 'is_locked']);
                
                return response()->json(['status' => TRUE, 'data' => $res], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 201);
            }
            
        }
        
        
        /**
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function skillDrag(Request $request)
        {
            $data = json_decode($request->data);
            if (count($data) > 0) {
                foreach ($data as $k => $val) {
                    $setting = SkillTabs::where('id', $val->id)->update(['sort_order' => ($k + 1)]);
                }
                $skill = SkillTabs::withCount('skills')->where(['is_valid' => 1, 'tab_type' => $request->tab_type])->orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->get();
                return response()->json(['status' => TRUE, 'data' => $skill], 200);
            }
        }
        
        /**
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function updateVisibilty(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'id'            => 'required',
                    'visiblityJson' => 'required',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                
                $skill = SkillTabs::where('id', $request->id)->update(['visible' => $request->visiblityJson]);
                return response()->json(['status' => ($skill) ? TRUE : FALSE, 'data' => []]);
            } catch (\Exception $e) {
                //throw $th;
                return response()->json(['s tatus' => TRUE, 'data' => $e->getMessage()], 200);
            }
        }
        
        /**
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function updateLock(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'id'      => 'required',
                    'lockVal' => 'required',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                
                $skill = SkillTabs::where('id', $request->id)->update(['is_locked' => ($request->lockVal) ? 0 : 1]);
                return response()->json(['status' => ($skill) ? TRUE : FALSE, 'data' => []]);
            } catch (\Exception $e) {
                //throw $th;
                return response()->json(['s tatus' => TRUE, 'data' => $e->getMessage()], 200);
            }
        }
        
        
        /**
         * @param int $userId
         * @return \Illuminate\Http\JsonResponse
         */
        public function getAllMandatory($userId = 0)
        {
            ////whereHas('skillTab', function ($q) {$q->where('is_locked', 0);})->
            try {
                if (Auth::user()->sub_role != 'C1') {
                    
                    
                    $res = skill::whereHas('skillTab', function ($q) {
                        $q->where('is_locked', 0)->where('tab_type', '!=', 5);
                    })->where('is_valid', 1)->where(function ($q) {
                        $q->orWhere('skill_format_id', 12);
                        $q->orWhere('skill_format_id', 13);
                        $q->orWhere('skill_format_id', 17);
                    })->with(['skillFormat' => function ($q) {
                        $q->select('id', 'name_en', 'name_fr');
                    }, 'skillImages', 'skillSelect', 'skillCheckBox', 'skillCheckBoxAcceptance', 'skillMeta'])->with(['userSkill' => function ($e) use ($userId) {
                        $e->where('user_id', (!empty($userId) ? $userId : Auth::user()->id));
                    }])->orderBy('sort_order', 'asc')->get(['id', 'name', 'short_name', 'skill_format_id', 'skill_tab_id']);
                } else {
                    $res = [];
                }
                return response()->json(['status' => TRUE, 'data' => $res], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 201);
            }
        }
        
    }
