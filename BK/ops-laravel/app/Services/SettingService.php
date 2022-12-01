<?php

/**
 * Created by PhpStorm.
 * User: Gourav Verma
 * Date: 16/02/2021
 */

namespace App\Services;

use App\Organisation;
use App\Setting;
use App\SuperadminSetting;
use App\User;
use Modules\Cocktail\Services\KctService;
use Modules\Cocktail\Services\V2Services\KctCoreService;

/**
 * -----------------------------------------------------------------------------------------------------------------
 * @description This service class is responsible for performing setting table related tasks
 * Setting table holds organisation account level data for each organisation (tenant account)
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class SettingService
 * @package App\Services
 */
class SettingService extends Service {
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To set the event module related Setting data
     * This will add the default values required when the module is enabled for the first time
     * And during update previous value this will
     *      -> fetch the previous setting
     *      -> update the keys value which needs to be updated
     *      -> save the data to setting so any other keys added will not be removed
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     */
    public function setEventSettings($request) {
        // to get the data to insert, either add default values if first time enabling, or get the existing data column
        $data = $this->getSettingForEvent($request);
        // to set the event word press data
        $data = $this->setSettingForEventWP($data, $request);
        // to set the event bluejeans related data
        $data = $this->setSettingForEventConference($data, $request);
        // to set the event KCT module related data
        $data = $this->setSettingForEventKCT($data, $request);
        
        // in the end updating the value with data
        Setting::updateOrCreate(
        // searching
            [
                'setting_key' => 'event_settings'
            ],
            // data to insert
            [
                'setting_key'   => 'event_settings',
                'setting_value' => json_encode($data),
            ]
        );
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the data from setting for event setting
     * @note if data is not present mostly for enabling first time,
     *      this will prepare the data with default values to store
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @return array
     */
    public function getSettingForEvent($request) {
        $setting = Setting::where('setting_key', 'event_settings')->first();
        if ($setting) {
            // event setting already present so fetch it to data so data variable will contain any other values and not overwritten
            $data = json_decode($setting->setting_value, 1);
        } else {
            // this is the first time event setting set .e.g. first time module is enabling
            // no setting found so set the default org admin part which will be handled from org admin setting
            $data = $this->setSettingForEventModuleFirstTime($request);
        }
        return $data;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description this will prepare the data for the event module when setting key does not exists
     * this will add default data that is required to load the module properly and as from requirements the default
     * values will be set for whole event module
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @return array
     */
    public function setSettingForEventModuleFirstTime($request) {
        $data['event_org_setting'] = [
            'default_organiser' => $this->getDefaultOrgAdminForEvent(),
            'prefix'            => $this->getDefaultPrefixForEvent($request->account_id),
        ];
        $data['event_virtual_org_setting'] = [
            'default_organiser' => $this->getDefaultOrgAdminForEvent(),
            'prefix'            => $this->getDefaultPrefixForEvent($request->account_id),
        ];
        $data['event_kct_setting'] = [
            'kct_max_participants' => $request->has('event_kct_max_participant') ? $request->event_kct_max_participant : '',
            'kct_keywords'         => $request->has('event_kct_keywords') ? $request->event_kct_keywords : '',
        ];
        $data = $this->setEventKCTDefaultValues($data);
        return $data;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to set the event module word press related data from the request
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param array $data
     * @param $request
     * @return array
     */
    public function setSettingForEventWP($data, $request) {
        $data['event_wp_setting'] = [
            'wp_url'  => ($request->has('event_wp_url') ? $request->event_wp_url : ''),
            'wp_pass' => ($request->has('event_wp_pass') ? $request->event_wp_pass : ''),
        ];
        return $data;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to set the event module bluejeans related data from the request
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param array $data
     * @param $request
     * @return array
     */
    public function setSettingForEventConference($data, $request) {
        $data['event_bluejeans_setting'] = [
            'bluejeans_event_client_id'     => ($request->has('event_bluejeans_client_id') ? $request->event_bluejeans_client_id : ''),
            'bluejeans_event_client_secret' => ($request->has('event_bluejeans_client_secret') ? $request->event_bluejeans_client_secret : ''),
            'bluejeans_event_client_email'  => $request->input('event_bluejeans_client_email', ''),
            'number_of_license'             => ($request->has('event_bluejeans_licenses') ? $request->event_bluejeans_licenses : 0),
        ];
        $data['event_zoom_setting'] =[
            'event_zoom_key' => ($request->has('event_zoom_key') ? $request->event_zoom_key: ''),
            'event_zoom_secret' => ($request->has('event_zoom_secret') ? $request->event_zoom_secret :''),
            'event_zoom_email' => ($request->has('event_zoom_email') ? $request->event_zoom_email:''),
        ];
        $data['event_current_conference'] = $this->findConferenceType($request);
        return $data;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to find the conference type set
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @return string|null
     */
    public function findConferenceType($request){
        if ($request->event_conference_type == 'bj'){
            $value = 'bj';
        }elseif($request->event_conference_type == 'zoom'){
            $value = 'zoom';
        }else{
            $value = null;
        }
        return $value;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to set the event module KCT related data from the request
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param array $data
     * @param $request
     * @return array
     */
    public function setSettingForEventKCT($data, $request) {
        $data['event_kct_setting']['kct_max_participants'] = $request->has('event_kct_max_participant') ? $request->event_kct_max_participant : '';
        $data['event_kct_setting']['kct_keywords']         = $request->has('event_kct_keywords') ? $request->event_kct_keywords : '';
        
        // setting the default value if not present as
        // this feature is introduced letter so existing account may not have this value
        // and the first time set will also not called cause values already present there.
        $data = $this->setEventKCTDefaultValues($data);
        return $data;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to get the default organiser for the event for first time
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return string
     */
    private function getDefaultOrgAdminForEvent() {
        $r = User::where('role', 'M1')->first();
        return (isset($r->id) ? $r->id : 'none');
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the default prefix for the event module for first time
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $accountId
     * @return string
     */
    private function getDefaultPrefixForEvent($accountId) {
        $o = Organisation::where('account_id', $accountId)->first();
        return (isset($o->acronym) ? $o->acronym : '---');
    }
    
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to set the default values for the KCT module.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $data
     * @return array
     */
    private function setEventKCTDefaultValues($data) {
        if (!isset($data['event_kct_setting']['kct_graphics_logo'])) {
            // the default logo for graphics customization is not present so add the default logo for kct.
            $data = $this->setEventKCTDefaultLogo($data);
        }
        if (!isset($data['event_kct_setting']['kct_graphics_color1'])) {
            // the color value is not present so add default values
            $data = $this->setEventKCTDefaultColor($data);
        }
        return $data;
    }
    
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This will get the main color values and set them to kct default color values.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param array $data
     * @return array
     */
    private function setEventKCTDefaultLogo($data) {
        $data['event_kct_setting']['kct_graphics_logo'] = KctCoreService::getInstance()->getDefaultLogoForKct();
        return $data;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This will get the main color values and set them to kct default color values.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param array $data
     * @return array
     */
    private function setEventKCTDefaultColor($data) {
        $colors = $this->getMainColor();
        $data['event_kct_setting']['kct_graphics_color1'] = $colors ? ['transparency' => $colors['color1']] : [];
        $data['event_kct_setting']['kct_graphics_color2'] = $colors ? ['transparency' => $colors['color2']] : [];
        return $data;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the main color of account set
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array|null
     */
    public function getMainColor() {
        $mainColor = SuperadminSetting::where('setting_key', 'graphic_config')->first();
        if ($mainColor) {
            $mainColor = json_decode($mainColor->setting_value, JSON_OBJECT_AS_ARRAY);
            if ($mainColor) {
                return [
                    'color1'  => $this->resolveColorToArray($mainColor, 'color1'),
                    'color2'  => $this->resolveColorToArray($mainColor, 'color2'),
                    'head_bg' => $this->resolveColorToArray($mainColor, 'headerColor1'),
                    'head_tc' => $this->resolveColorToArray($mainColor, 'headerColor2'),
                ];
            }
        }
        return null;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to safely convert object to rgba values
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $colors
     * @param $key
     * @return null[]
     */
    private function resolveColorToArray($colors, $key) {
        return [
            'r' => isset($colors[$key]['r']) ? $colors[$key]['r'] : null,
            'g' => isset($colors[$key]['g']) ? $colors[$key]['g'] : null,
            'b' => isset($colors[$key]['b']) ? $colors[$key]['b'] : null,
            'a' => isset($colors[$key]['a']) ? $colors[$key]['a'] : null,
        ];
    }
}
