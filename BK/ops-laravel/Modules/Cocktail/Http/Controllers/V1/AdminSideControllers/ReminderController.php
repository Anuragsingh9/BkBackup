<?php

namespace Modules\Cocktail\Http\Controllers\V1\AdminSideControllers;

use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Cocktail\Http\Requests\V1\UpdateReminderRequest;
use Validator;


class ReminderController extends Controller {
    
    public function get(Request $request) {
        try {
            $setting = Setting::where('setting_key', "event_reminders")->first();
            if (!$setting) {
                return response()->json(['status' => false, 'msg' => 'No Data Found', 'data' => []], 200);
            }
            $settingValue = json_decode($setting->setting_value);
            return response()->json(['data' => $settingValue, 'status' => true], 200);
            
        } catch (Exception $e) {
            return reponse()->json(['status' => false, 'msg' => "Internal Server Error"], 500);
        }
    }
    
    public function update(UpdateReminderRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $settingKey = config('cocktail.setting_keys.reminder_settings'); // the setting key on which data will update
            
            // these keys in setting value can be updated as to protect from adding uneven data from user
            $possibleUpdateKeys = array_keys(config('cocktail.setting_keys.reminder_mails'));
            
            $setting = Setting::where('setting_key', $settingKey)->first();
            if (!$setting) {
                return response()->json(['status' => false, 'msg' => 'No Data Found', 'data' => []], 200);
            }
            // as setting values are in json encoded and this column is not also casted to array
            $settingValue = json_decode($setting->setting_value, 1);
            
            // for each possible key
            // this will search each key in db setting value
            // and in that it will add request value.
            foreach ($possibleUpdateKeys as $key) {
                if (isset($settingValue['reminders'][$key])) {
                    $settingValue['reminders'][$key]['active'] = $request->data['reminders'][$key]['active'];
                    $settingValue['reminders'][$key]['days'] = $request->data['reminders'][$key]['days'];
                }
            }
            
            // to keep the result for sending back
            $result = $settingValue;
            
            $settingValue = json_encode($settingValue);
            
            if (!Setting::where('setting_key', $settingKey)->update(['setting_value' => $settingValue])) {
                // if not updated send error
                return reponse()->json(['status' => false, 'msg' => "Internal Server Error"], 500);
            }
            DB::connection('tenant')->commit();
            return response()->json(['data' => $result, 'status' => true], 200);
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => 'Internal Server Error', 'error' => $e->getMessage()], 500);
        }
    }
}
