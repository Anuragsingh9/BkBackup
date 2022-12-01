<?php
    
    namespace Modules\Crm\Http\Controllers;
    
    use Illuminate\Http\Request;
    use Illuminate\Routing\Controller;
    use Modules\Crm\Entities\AssistanceTeam;
    use Modules\Crm\Entities\Assistance;
    use App\Workshop;
    use App\User;
    
    use App\WorkshopMeta;
    use DB;
    
    use Validator;
    
    class AssisTeamController extends Controller
    {
        private $core, $work;
        
        public function __construct()
        {
            $this->core = app(\App\Http\Controllers\CoreController::class);
            $this->work = app(\App\Http\Controllers\WorkshopController::class);
        }
        
        /**
         * Display a listing of the resource.
         * fetch Team's all recoords,
         * with Assistance:id,assistance_type_name,
         * with user:id,username.
         *
         * @return \Illuminate\Http\Response
         */
        public function index()
        {
            try {
                $assisTeam = AssistanceTeam::with('assistance_type:id,assistance_type_name')->with('user:id,fname')->get(['id', 'assistance_type_id', 'member_id', 'created_at']);
                $assisTeam = $assisTeam->unique(function ($item) {
                    return $item['assistance_type_id'] . $item['member_id'];
                })->values()->all();
                
                return response()->json(['status' => TRUE, 'data' => $assisTeam], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
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
         * @param assistance_type_id as assistance_id
         * @param member_id as user_id
         * @param \Illuminate\Http\Request $request
         * @return \Illuminate\Http\Response
         */
        public function store(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'assistance_type_id' => 'required|exists:tenant.crm_assistance_type,id',
                    'member_id'          => 'required|exists:tenant.users,id',
                ]);
                $validator->after(function ($validator) use ($request) {
                    if ($this->memberAssisTypeunique($request->assistance_type_id, $request->member_id)) {
                        $validator->errors()->add('field', 'This Member is already in Team with Selected Type');
                    }
                });
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 400);
                }
                DB::connection('tenant')->beginTransaction();
                $assisTeam = AssistanceTeam::create([
                    'assistance_type_id' => request('assistance_type_id'),
                    'member_id'          => request('member_id'),
                ]);
                // fetch Assistance short_name with assistance_type_id
                $assistance = Assistance::where('id', $request->assistance_type_id)->first(['assistance_type_short_name']);
                //  fetch workshop_id with Assistance short_name
                $workshop = Workshop::where('code1', $assistance->assistance_type_short_name)->first(['id']);
                //Create an entry in WorkshopMeta where role => 0 after create Assistacne team member
                $workshopMeta = WorkshopMeta::create(['workshop_id' => $workshop->id, 'user_id' => $assisTeam->member_id, 'role' => 0, 'meeting_id' => NULL]);
                $workshop_data = Workshop::with('meta')->find($workshop->id);
                
                $dataMail = $this->work->getMailData($workshop_data, 'commission_new_user');
                
                $subject = $dataMail['subject'];
                $route_members = $dataMail['route_members'];
                /*@todo*/
                //this is commented and we need to update it as per new working
                //$this->alertNewMember($workshop_data, $newmember);
                $user = User::select('email')->find(request('member_id'));
                
                $mailData['mail'] = ['subject' => ($subject), 'emails' => [$user->email], 'workshop_data' => $workshop_data, 'url' => $route_members];
                $this->core->SendMassEmail($mailData, 'new_commission_user');
                DB::connection('tenant')->commit();
                return response()->json(['status' => TRUE, 'msg' => 'Record Inserted Successfully.', 'data' => $assisTeam, 'workshopMeta' => $workshopMeta], 200);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
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
        }
        
        /**
         * Show the form for editing the specified resource.
         *
         * @param int $id
         * @return \Illuminate\Http\Response
         */
        // public function edit($id)
        // {
        //     try {
        //         $assisTeam = AssistanceTeam::find($id);
        //         if (!$assisTeam) {
        //             return response()->json(['status' => false, 'msg' => 'not found'], 200);
        //         }
        //         // dd($assisTeam);
        //         return response()->json(['status' => true, 'data' => $assisTeam], 200);
        //     } catch (\Exception $e) {
        //         return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        //     }
        // }
        
        /**
         * Update the specified resource in storage.
         *
         * @param \Illuminate\Http\Request $request
         * @param int $id
         * @return \Illuminate\Http\Response
         */
        public function update(Request $request, $id)
        {
            //
        }
        
        /**
         * Remove the specified resource from storage.
         *
         * @param int $id
         * @return \Illuminate\Http\Response
         */
        public function destroy($id)
        {
            try {
                $assisTeam = AssistanceTeam::find($id)->delete();
                return response()->json(['status' => TRUE, 'msg' => 'Redord Deleted Succcessfully'], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'data' => $assisTeam, 'msg' => $e->getMessage()], 500);
            }
        }
        
        
        public function getFilteredUser($val, $role)
        {
            
            try {
                $data = User::where('role', '!=', 'M3')->where(DB::raw('CONCAT(email," ", lname," ",fname)'), 'like', '%' . $val . '%')
                    ->where('permissions->' . $role, 1)->groupBy('email')->get();
                
                return response()->json(['status' => TRUE, 'msg' => '', 'data' => $data], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'data' => '', 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function memberAssisTypeunique($assId, $memId)
        {
            $count = AssistanceTeam::where(['assistance_type_id' => $assId, 'member_id' => $memId])->count();
            return ($count == 0) ? FALSE : TRUE;
            
        }
    }
    
    // $mailData['mail'] = ['subject' => $subject, 'email' => $userArray['email'], 'password' => $userArray['password'], 'url' => $route];
    // $this->core->SendEmail($mailData, 'new_user');