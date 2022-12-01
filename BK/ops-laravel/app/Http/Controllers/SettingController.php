<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use DB;
use Session;
use App\Setting;

class SettingController extends Controller {

    private $core;

    public function __construct() {
        $this->core = app(\App\Http\Controllers\CoreController::class);
    }

    public function updateSetting(Request $request) {
        $last_rec = Setting::updateOrCreate(['setting_key' => $request->setting_key], ['setting_key' => $request->setting_key, 'setting_value' => $request->setting_value]);
        return response()->json($last_rec);
    }

    public function updateGraphicSetting(Request $request) {
        $status = 0;
        if ($request->hasFile('file')) {
            $domain = strtok($_SERVER['SERVER_NAME'], '.');
            $folder = $domain . '/uploads';
            $filename = $this->core->fileUploadByS3($request->file('file'), $folder, 'public');
            $data['setting_value'] = json_encode(['icon' => $filename]);
            if (Setting::where('setting_key', $request->setting_key)->update($data)) {
                $status = 1;
            }
        }
        return response()->json($status);
    }

    public function updateEmailGraphic(Request $request) {
        $pre_data = getSettingData('email_graphic');
        $postData = ['email_sign' => $request->email_sign];
        $domain = strtok($_SERVER['SERVER_NAME'], '.');
        if ($request->hasFile('email_top_banner')) {
            $folder = $domain . '/uploads';
            $postData['top_banner'] = $this->core->fileUploadByS3($request->file('email_top_banner'), $folder, 'public');
        } else {
            $postData['top_banner'] = $pre_data->top_banner;
        }
        if ($request->hasFile('email_bottom_banner')) {
            $folder = $domain . '/uploads';
            $postData['bottom_banner'] = $this->core->fileUploadByS3($request->file('email_bottom_banner'), $folder, 'public');
        } else {
            $postData['bottom_banner'] = $pre_data->bottom_banner;
        }
        $last_rec = Setting::where('setting_key', $request->setting_key)->update(['setting_value' => json_encode($postData)]);
        return response()->json($last_rec);
    }
    public function removeEmailGraphic(Request $request){
        $pre_data = getSettingData('email_graphic');
        $postData = ['email_sign' => $request->email_sign];
        $domain = strtok($_SERVER['SERVER_NAME'], '.');
        if ($request->type=='email_top_banner') {
            $postData['top_banner'] ='';
        }
        else{
            $postData['top_banner'] =$pre_data->top_banner;
        }
        if ($request->type=='email_bottom_banner') {
            $postData['bottom_banner'] = '';
        }
        else{
            $postData['bottom_banner'] =$pre_data->bottom_banner;
        }
        $last_rec = Setting::where('setting_key', $request->setting_key)->update(['setting_value' => json_encode($postData)]);
        return response()->json($last_rec);
    }
    public function updatePdfGraphic(Request $request) {
        $pre_data = getSettingData('pdf_graphic');
        $postData = ['color1' => json_decode($request->color1), 'color2' => json_decode($request->color2), 'footer_line1' => $request->footer_line1, 'footer_line2' => $request->footer_line2];
        if ($request->hasFile('header_logo')) {
            $domain = strtok($_SERVER['SERVER_NAME'], '.');
            $folder = $domain . '/uploads';
            $postData['header_logo'] = $this->core->fileUploadByS3($request->file('header_logo'), $folder, 'public');
        } else {
            $postData['header_logo'] = $pre_data->header_logo;
        }
        $last_rec = Setting::where('setting_key', $request->setting_key)->update(['setting_value' => json_encode($postData)]);
        return response()->json($last_rec);
    }

    public function getSetting(Request $request) {
        $newData = [];
        $data = Setting::where('setting_key', $request->setting_key)->first();
        if ($request->setting_key == 'platform_graphic') {
            $json_decode = json_decode($data->setting_value);
            if (isset($json_decode->icon) && $json_decode->icon != '') {
                $json_decode->icon = $this->core->getS3Parameter($json_decode->icon, 2);
                $newData['icon'] = isset($json_decode->icon)?$json_decode->icon:'';
            }
        } else if ($request->setting_key == 'push_notification_graphic') {
            $json_decode = json_decode($data->setting_value);
            if (isset($json_decode->icon) && $json_decode->icon != '') {
                $json_decode->icon = $this->core->getS3Parameter($json_decode->icon, 2);
                $newData['icon'] = isset($json_decode->icon)?$json_decode->icon:'';
            }
        } else if ($request->setting_key == 'pdf_graphic') {
            $json_decode = json_decode($data->setting_value);
            if (isset($json_decode->header_logo) && $json_decode->header_logo != '') {
                $json_decode->header_logo = $this->core->getS3Parameter($json_decode->header_logo, 2);
                $newData['header_logo'] = isset($json_decode->header_logo)?$json_decode->header_logo:'';
            }
            $newData['footer_line1'] = isset($json_decode->footer_line1)?$json_decode->footer_line1:'';
            $newData['footer_line2'] = isset($json_decode->footer_line2)?$json_decode->footer_line2:'';
            $newData['color1'] = isset($json_decode->color1)?$json_decode->color1:'';
            $newData['color2'] = isset($json_decode->color2)?$json_decode->color2:'';
            $newData['pdf_switch'] = isset($json_decode->pdf_switch)?$json_decode->pdf_switch:'';
        } else if ($request->setting_key == 'email_graphic') {
            $json_decode = json_decode($data->setting_value);
            if (isset($json_decode->top_banner) && $json_decode->top_banner != '') {
                $json_decode->top_banner = $this->core->getS3Parameter($json_decode->top_banner, 2);
                $newData['top_banner'] = isset($json_decode->top_banner)?$json_decode->top_banner:'';
            }
            if (isset($json_decode->bottom_banner) && $json_decode->bottom_banner != '') {
                $json_decode->bottom_banner = $this->core->getS3Parameter($json_decode->bottom_banner, 2);
                $newData['bottom_banner'] = isset($json_decode->bottom_banner)?$json_decode->bottom_banner:'';
            }

            $newData['email_sign'] = isset($json_decode->email_sign)?$json_decode->email_sign:'';
        }
        if (!empty($newData))
            $data->setting_value = json_encode($newData);

        return response()->json($data);
    }


    public function getDashboardSetting(Request $request)
    {
        $setting = Setting::where('setting_key', 'dashboard_setting')->first();
        if(isset($request->show_all)){
            $json_decode = json_decode($setting->setting_value);
        $data = collect($json_decode)->sortBy('order');
          
        return response()->json($data->toArray());
        }else{
         $json_decode = json_decode($setting->setting_value);

        $data = collect($json_decode)->reject(function ($user) {
            return $user->is_show == 0;
        })->sortBy('order');

        return response()->json($data->toArray());
        }
        
    }


    public function dashboardSetting(Request $request)
    {
        $data = json_decode($request->data);
        if (count($data) > 0) {
            foreach ($data as $k => $val) {
                $newData[] = ['name' => $val->name, 'order' => ($k + 1), 'is_show' => $val->is_show];
            }
            $setting = Setting::where('setting_key', 'dashboard_setting')->first();
            $setting->setting_value = json_encode(($newData));
            $setting->save();

            $settingNew = Setting::where('setting_key', 'dashboard_setting')->first();
            $json_decode = json_decode($setting->setting_value);
            $dataNew = collect($json_decode)->sortBy('order');
                return response()->json($dataNew->toArray());
        }

    }

    public function updateDashboardCheckedSetting(Request $request)
    {
        //var_dump($request->is_show);exit;
        if (!empty($request->is_show) && !empty($request->name)) {

            $setting = Setting::where('setting_key', 'dashboard_setting')->first();
            $json_decode = json_decode($setting->setting_value);

            foreach ($json_decode as $item) {
                if (trim($request->name) == $item->name) {
                    $item->name = trim($request->name);
                    $item->is_show = (($request->is_show=='true') ? 1 : 0);
                }
            }
            $setting->setting_value = json_encode(($json_decode));
            $setting->save();

            $settingNew = Setting::where('setting_key', 'dashboard_setting')->first();
            $settingNew = Setting::where('setting_key', 'dashboard_setting')->first();
            $json_decode = json_decode($setting->setting_value);
            $dataNew = collect($json_decode)->sortBy('order');
                return response()->json($dataNew->toArray());
        }

    }
    public function updatePasswordGraphicSetting(Request $request)
    {
        if (isset($request->setting_key)) {
            $lang = $request->setting_key;
        } else {
            if (session()->has('lang')) {
                $lang = 'forgot_password_' . session()->get('lang');
            } else {
                $lang = 'forgot_password_FR';
            }
        }
        $pre_data = getSettingData($lang);
        $postData = ['header_bar' => $request->header_bar, 'welcome_text1' => $request->welcome_text1, 'welcome_text2' => $request->welcome_text2, 'change_text' => $request->change_text, 'caption_text1' => $request->caption_text1, 'caption_text2' => $request->caption_text2, 'button_text1' => $request->button_text1];

        $last_rec = Setting::where('setting_key', $lang)->update(['setting_value' => json_encode($postData)]);
        return response()->json($last_rec);
    }

}
