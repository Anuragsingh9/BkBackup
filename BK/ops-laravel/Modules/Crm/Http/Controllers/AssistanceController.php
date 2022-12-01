<?php
    
    namespace Modules\Crm\Http\Controllers;
    
    use App\AccountSettings;
    use Illuminate\Http\Request;
    use Illuminate\Routing\Controller;
    use Illuminate\Support\Facades\Auth;
    use Modules\Crm\Entities\AssistanceTeam;
    use Validator;
    use Modules\Crm\Entities\Assistance;
    use App\Workshop;
    use DB;
    
    
    class AssistanceController extends Controller
    {
        private $super;
        
        public function __construct()
        {
            $this->super = app(\App\Services\SuperAdmin::class);
            
        }
        
        
        /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function index($field = NULL)
        {
            try {
                //$field is optional parameter = assistance_type_name
                //if $field is requested then fetch id and $field name
                if (!in_array(Auth::user()->role, ['M1', 'M0'])) {
                    $ids = AssistanceTeam::where('member_id', Auth::user()->id)->pluck('assistance_type_id');
                    if ($field) {
                        $assistance = Assistance::whereIn('id', $ids)->get(['id', $field]);
                        return response()->json(['status' => TRUE, 'data' => $assistance], 200);
                    }
                    $assistance = Assistance::whereIn('id', $ids)->get(['id', 'assistance_type_name', 'assistance_type_short_name', 'created_at']);
                } else {
                    $assistance = Assistance::get(['id', 'assistance_type_name', 'assistance_type_short_name', 'created_at']);
                }
                //if $field = null
                return response()->json(['status' => TRUE, 'data' => $assistance], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
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
                //validate data with Validator
                $validator = Validator::make($request->all(), [
                    'assistance_type_name' => 'required|regex:/^[0-9a-zA-Zu00E0-u00FC&àâäèéêëîïôœùûüÿççÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ _\'\ "-]*$/m|min:3|max:50',
                    'assistance_type_short_name' => 'required|regex:/^[0-9a-zA-Zu00E0-u00FC&àâäèéêëîïôœùûüÿççÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ _\'\ "-]*$/m|min:2|max:5|unique:tenant.crm_assistance_type,assistance_type_short_name',
                ]);
                
                //chek if validator fails
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 400);
                }
                
                DB::connection('tenant')->beginTransaction();
                // Create Data into database after data validate successfully
                $assistance = Assistance::create([
                    'assistance_type_name'       => request('assistance_type_name'),
                    'assistance_type_short_name' => request('assistance_type_short_name'),
                ]);
                //if workshop with requested code1 is already exists ,workshop not creaeted
                if (Workshop::where('code1', $assistance->assistance_type_short_name)->exists()) {
                    return response()->json(['status' => FALSE, 'msg' => 'code1 is already in use!']);
                }
                /**
                 * create a workshop
                 * @param int $type
                 * @param string $name
                 * @param mixed $code1
                 * @return \Illuminate\Http\JsonResponse
                 */
                
                $workshop = $this->super->createCommission(1, $assistance->assistance_type_name, $assistance->assistance_type_short_name, config('constants.PROJECT'));
                
                // if workshop not created ,rollback all queries
                if (!$workshop) {
                    DB::connection('tenant')->rollback();
                    return response()->json(['status' => FALSE, 'msg' => 'Failed to create'], 200);
                }
                DB::connection('tenant')->commit();
                // return response in json if Assistance ,Workshop and project successfully created
                return response()->json(['status' => TRUE, 'msg' => 'Record Inserted Successfully.', 'data' => $assistance], 200);
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
        public function edit($id)
        {
            try {
                //fetch single record for edit
                $assistance = Assistance::find($id);
                //check if data is empty
                if (!$assistance) {
                    return response()->json(['status' => FALSE, 'msg' => 'Not found'], 200);
                }
                return response()->json(['status' => TRUE, 'data' => $assistance], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
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
                //validate data with Validator
                $validator = Validator::make($request->all(), [
                    'assistance_type_name' => 'required|regex:/[a-zA-Z0-9\s]+/|min:3|max:50',
                ]);
                //chek if validator fails
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 400);
                }
                DB::connection('tenant')->beginTransaction();
                // Create Data into database after data validate successfully
                $assistance = Assistance::where('id', $id)->update([
                    'assistance_type_name' => request('assistance_type_name'),
                ]);
                if ($assistance) {
                    $data = Assistance::where('id', $id)->first(['assistance_type_short_name']);
                    $workshop = Workshop::where('code1', $data->assistance_type_short_name)->update(['workshop_name' => $request->assistance_type_name]);
                    
                    DB::connection('tenant')->commit();
                    return response()->json(['status' => TRUE, 'msg' => 'Record Updated Successfully!', 'data' => $assistance, $workshop], 200);
                } else {
                    DB::connection('tenant')->rollback();
                    return response()->json(['status' => FALSE, 'msg' => 'not updated'], 200);
                }
                
                
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
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
            DB::connection('tenant')->beginTransaction();
            try {
                $assistance = Assistance::find($id)->delete();
                DB::connection('tenant')->commit();
                return response()->json(['status' => TRUE, 'msg' => 'Record Deleted Successfully'], 200);
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'data' => $assistance, 'msg' => $e->getMessage()], 500);
            }
        }
    }
