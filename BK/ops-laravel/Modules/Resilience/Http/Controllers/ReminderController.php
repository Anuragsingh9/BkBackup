<?php

namespace Modules\Resilience\Http\Controllers;

use DB;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Resilience\Http\Requests\UpdateReminderRequest;


class ReminderController extends Controller
{

    public function __construct()
    {
        //
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $setting = Setting::where('setting_key', "consultation_reminder")->first();
        if(!$setting) {
            return response()->json(['status' => false, 'msg' => 'No Data Found', 'data' => []], 200);
        }
        $settingValue = json_decode($setting->setting_value);
        return response()->json(['data' => $settingValue, 'status' => true], 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param UpdateReminderRequest $request
     * @return void
     */
    public function update(UpdateReminderRequest $request)
    {
        $reminder = Setting::where('setting_key', "consultation_reminder")->first();
        if(!$reminder) {
            return response()->json(['status' => false, 'msg' => 'Reminder Page Data Not Found', 'data' => []], 200);
        }
        $data = $request->all();
//        unset($data['_method']);
        try {
            DB::connection('tenant')->beginTransaction();
            $remind = Setting::updateOrCreate(['setting_key' => 'consultation_reminder'], ['setting_value'=>json_encode($data['data'])]);
            DB::connection('tenant')->commit();
            return response()->json(['data' => json_decode($remind->setting_value), 'status' => true], 200);
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
        }
    }

}
