<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use DB;
use App\SuperadminSetting;
use App\Setting;

class SuperadminSettingController extends Controller {

    private $core;

    public function __construct() {
        $this->core = app(\App\Http\Controllers\CoreController::class);
    }

    public function updateGraphicSetting(Request $request) {
        $pre_data = getSettingData('graphic_config', 1);
        $postData = ['pdf_switch'=>$request->pdf_switch,'headerColor1' => json_decode($request->headerColor1), 'headerColor2' => json_decode($request->headerColor2),'color1' => json_decode($request->color1), 'color2' => json_decode($request->color2), 'transprancy1' => json_decode($request->transprancy1), 'transprancy2' => json_decode($request->transprancy2), 'header_logo' => $request->header_logo, 'right_header_icon' => $request->right_header_icon];
        if ($request->hasFile('header_logo')) {
            $folder = 'uploads';
            $postData['header_logo'] = $this->core->fileUploadByS3($request->file('header_logo'), $folder, 'public');
        } else {
            $postData['header_logo'] = $pre_data->header_logo;
        }
        if ($request->hasFile('right_header_icon')) {
            $folder = 'uploads';
            $postData['right_header_icon'] = $this->core->fileUploadByS3($request->file('right_header_icon'), $folder, 'public');
        } else {
            $postData['right_header_icon'] = $pre_data->right_header_icon;
        }

        $last_rec = SuperadminSetting::where('setting_key', $request->setting_key)->update(['setting_value' => json_encode($postData)]);

        if ($request->pdf_switch == '1') {
            $pre_data = getSettingData('pdf_graphic');
            $postData = ['pdf_switch'=>$request->pdf_switch,'color1' => json_decode($request->color1), 'color2' => json_decode($request->color2), 'footer_line1' => isset($pre_data->footer_line1)?$pre_data->footer_line1:NULL, 'footer_line2' => isset($pre_data->footer_line2)?$pre_data->footer_line2:NULL];
            if ($request->hasFile('header_logo')) {
                $folder = 'uploads';
                $postData['header_logo'] = $this->core->fileUploadByS3($request->file('header_logo'), $folder, 'public');
            } else {
                $postData['header_logo'] = $pre_data->header_logo;
            }
            $last_rec = Setting::where('setting_key', 'pdf_graphic')->update(['setting_value' => json_encode($postData)]);
        }

        return response()->json($last_rec);
    }

    public function getSetting(Request $request) {
        $newData = [];
        $data = SuperadminSetting::where('setting_key', $request->setting_key)->first();
        if ($request->setting_key == 'graphic_config') {
            $json_decode = json_decode($data->setting_value);
            if ($json_decode->header_logo != '') {
                $json_decode->header_logo = $this->core->getS3Parameter($json_decode->header_logo, 2);
                $newData['header_logo'] = $json_decode->header_logo;
            }
            if ($json_decode->right_header_icon != '') {
                $json_decode->right_header_icon = $this->core->getS3Parameter($json_decode->right_header_icon, 2);
                $newData['right_header_icon'] = $json_decode->right_header_icon;
            }
            $newData['color1'] = $json_decode->color1;
            $newData['headerColor1'] = $json_decode->headerColor1;
            $newData['color2'] = $json_decode->color2;
            $newData['headerColor2'] = $json_decode->headerColor2;
            $newData['transprancy1'] = $json_decode->transprancy1;
            $newData['transprancy2'] = $json_decode->transprancy2;
            $newData['pdf_switch'] = isset($json_decode->pdf_switch)?$json_decode->pdf_switch:'';
        }

        if (!empty($newData))
            $data->setting_value = json_encode($newData);

        return response()->json($data);
    }

    public function logoDelete(){
        $pre_data = getSettingData('graphic_config', 1);
        
        $pre_data->header_logo='';
        
   $last_rec = SuperadminSetting::where('setting_key', 'graphic_config')->update(['setting_value' => json_encode($pre_data)]);
   return response()->json(['status' => 1, 'msg' => "Logo Delete Successfully."]);
    }
}
