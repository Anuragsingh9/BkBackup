<?php

namespace App\Services;

use App\Project;
use App\Setting;
use App\Workshop;
use Modules\Crm\Entities\TranscribeTracking;
use Modules\Newsletter\Entities\AdobePhotosTracking;

class AccountSettingService extends Service {
    /**
     * If need to just set setting only use checkValSet or direct request if text
     * if need to do additional setting then call a method there which does the extra job and return what to save in db
     * keep this method only assigning value to post data do not add any functionality here
     * instead put the key here and call the method which return its value
     *
     * @param $rec
     * @param $request
     * @param $postData
     */
    public function setAccountSettingNewWay($rec, $request, &$postData) {
        $postData['setting'] = [
            // newsletter module
            "news_letter_enable"          => $this->settingNewsLetter($rec, $request),
            "manage_template"             => checkValSet($request->manage_template),
            "stock_setting"               => $this->settingStock($rec, $request),
            // crm module
            "crm_enable"                  => $this->settingCrm($rec, $request),
            "transcribe_setting"          => $this->settingTranscribe($rec, $request),
            "instance_enable"             => $this->settingInstance($rec, $request),
            "press_enable"                => $this->settingPress($rec, $request),
            // event module
            "event_enabled"               => checkValSet($request->event_enable),
            "event_settings"              => $this->settingEvent($rec, $request),
            // workshop graphics
            "workshop_graphic_enable"     => checkValSet($request->workshop_graphic_enable),
            "workshops_enable"            => checkValSet($request->workshops_enable),
            "documents_enable"            => checkValSet($request->documents_enable),
            // icontact settings
            "direct_video_enable"         => checkValSet($request->direct_video_enable),
            "ICONTACT_API_APP_ID"         => isset($request->ICONTACT_API_APP_ID) ? $request->ICONTACT_API_APP_ID : '',
            "ICONTACT_API_PASSWORD"       => isset($request->ICONTACT_API_PASSWORD) ? $request->ICONTACT_API_PASSWORD : '',
            "ICONTACT_API_USERNAME"       => isset($request->ICONTACT_API_USERNAME) ? $request->ICONTACT_API_USERNAME : '',
            "ICONTACT_CLIENT_FOLDER_ID"   => isset($request->ICONTACT_CLIENT_FOLDER_ID) ? $request->ICONTACT_CLIENT_FOLDER_ID : '',
            "ICONTACT_ACCOUNT_ID"         => isset($request->ICONTACT_ACCOUNT_ID) ? $request->ICONTACT_ACCOUNT_ID : '',
            // qualification module
            "qualification_enable"        => checkValSet($request->qualification_module_enable),
            // messenger module
            "messenger_enable"            => checkValSet($request->messenger_enable),
            // org setting
            "organiser_setting_enable"    => checkValSet($request->organiser_setting_enable),
            // Workshop meeting setting
            "video_meeting_enable"        => $this->settingVideoMeeting($rec, $request),
            // vertical bar settings
            "vertical_bar_enable"         => checkValSet($request->vertical_bar_enable), //  if this off turn of all other
            "add_module_enable"           => checkValSet($request->vertical_bar_enable) ? checkValSet($request->add_module_enable) : 0,
            "vertical_messenger_enable"   => checkValSet($request->vertical_bar_enable) ? checkValSet($request->vertical_messenger_enable) : 0,
            "feature_request_enable"      => checkValSet($request->vertical_bar_enable) ? checkValSet($request->feature_request_enable) : 0,
            "help_enable"                 => checkValSet($request->vertical_bar_enable) ? checkValSet($request->help_enable) : 0,
            "share_enable"                => checkValSet($request->vertical_bar_enable) ? checkValSet($request->share_enable) : 0,
            "others_enable"               => checkValSet($request->vertical_bar_enable) ? checkValSet($request->others_enable) : 0,
            "vertical_event_enabled"      => checkValSet($request->vertical_bar_enable) ? checkValSet($request->vertical_event_enabled) : 0,
            "vertical_news_letter_enable" => checkValSet($request->vertical_bar_enable) ? checkValSet($request->vertical_news_letter_enable) : 0,
            // Reinvent Module
            "consultation_enable"         => $this->settingConsultation($rec, $request),
            "reinvent_enable"             => checkValSet($request->reinvent_enable),
        ];
        // if need to decode setting
        
    }
    
    public function settingNewsLetter($rec, $request) {
        $code = 'NSL';
        $workshopName = 'Newsletter';
        $this->checkAndAddWorkshop($code, $workshopName, $rec->account_id, $rec->project_enable);
        // updating so also update the workshop display column
        if (isset($rec->setting['news_letter_enable'])) { // to display column of project and workshop according to news letter enable disable if in db already
            Workshop::where('code1', 'NSL')
                ->withoutGlobalScopes()
                ->update(['display' => checkValSet($request->news_letter_enable)]);
            $workshops = Workshop::whereIn('code1', [$code])->withoutGlobalScopes()->get(['id']);
            if (count($workshops) > 0) { // set the project display also as enable disable
                Project::where('wid', $workshops->pluck('id'))
                    ->withoutGlobalScopes()
                    ->update(['display' => checkValSet($request->news_letter_enable)]);
            }
        }
        // now send the value which will be set in account setting
        return checkValSet($request->news_letter_enable);
    }
    
    public function settingCrm($rec, $request) {
        $code = 'CRM';
        $workshopName = 'CRM';
        $this->checkAndAddWorkshop($code, $workshopName, $rec->account_id, $rec->project_enable);
        if (isset($rec->setting['crm_enable'])) {
            Workshop::where('code1', 'CRM')
                ->withoutGlobalScopes()
                ->update(['display' => checkValSet($request->crm_enable)]);
            $workshops = Workshop::whereIn('code1', [$code])->withoutGlobalScopes()->get(['id']);
            if (count($workshops) > 0) { // set the project display also as enable disable
                Project::where('wid', $workshops->pluck('id'))
                    ->withoutGlobalScopes()
                    ->update(['display' => checkValSet($request->crm_enable)]);
            }
        }
        return checkValSet($request->crm_enabled);
    }
    
    public function settingEvent($rec, $request) {
        return [
            "wp_enabled"          => $request->event_enable ? checkValSet($request->event_wp_enabled) : 0,
            "bluejeans_enabled"   => $request->event_enable ? checkValSet($request->event_bluejeans_enabled) : 0,
            "keep_contact_enable" => $request->event_enable ? checkValSet($request->event_keep_contact_enabled) : 0,
        ];
    }
    
    public function settingStock($rec, $request) {
        $stock_available_credit = isset($request->stock_available_credit)
            ? $request->stock_available_credit
            : (($rec->setting['stock_setting']['available_credit']) ?? 0);
        $stock_max = isset($request->stock_allowed_number)
            ? $request->stock_allowed_number :
            (($rec->setting['stock_setting']['max_allowed']) ?? 0);
        $stock_renewal = isset($request->stock_renewal)
            ? $request->stock_renewal
            : (($rec->setting['stock_setting']['stock_renewal']) ?? 1);
        
        return [
            "enabled"          => checkValSet($request->stock_setting_enabled),
            "max_allowed"      => $stock_max,
            "available_credit" => $stock_available_credit,
            "renewal_date"     => $stock_renewal,
        ];
    }
    
    public function settingTranscribe($rec, $request) {
        $transcribe_max = isset($request->transcribe_allowed_number)
            ? $request->transcribe_allowed_number
            : (($rec->setting['transcribe_setting']['max_allowed']) ?? 0);
        $transcribe_available_credit = isset($request->transcribe_available_credit)
            ? $request->transcribe_available_credit
            : (($rec->setting['transcribe_setting']['available_credit']) ?? 0);
        
        return [
            "enabled"          => checkValSet($request->transcribe_setting_enabled),
            "max_allowed"      => $transcribe_max,
            "available_credit" => $transcribe_available_credit,
        ];
    }
    
    public function settingInstance($rec, $request) {
        $condition = empty($rec->setting) ? 1 : checkValSet($request->instance_enable);
        $this->enableCrmForInstancePress($condition, $rec);
        return checkValSet($request->instance_enable);
    }
    
    public function settingPress($rec, $request) {
        $condition = empty($rec->setting) ? 1 : checkValSet($request->press_enable);
        $this->enableCrmForInstancePress($condition, $rec);
        return checkValSet($request->press_enable);
    }
    
    public function settingVideoMeeting($rec, $request) {
        if (!(isset($rec->setting['video_meeting_enable']) || isset($request->client_id) || isset($request->client_secret))) {
            $settingValues['client_id'] = env('BLUEJEANS_DEFAULT_ID');
            $settingValues['client_secret'] = env('BLUEJEANS_DEFAULT_SECRET');
            $settingValues['number_of_license'] = 1;
        } else {
            $settingValues = [
                'client_id'         => isset($request->client_id) ? $request->client_id : null,
                'client_secret'     => isset($request->client_secret) ? $request->client_secret : null,
                'number_of_license' => isset($request->vm_bluejeans_licenses) ? $request->vm_bluejeans_licenses : null,
            ];
        }
        $data = [
            'setting_key'   => 'video_meeting_api_setting',
            'setting_value' => json_encode($settingValues),
        ];
        $key = 'video_meeting_api_setting';
        Setting::updateOrCreate(['setting_key' => $key], $data);
        return checkValSet($request->video_meeting_enable);
    }
    
    public function settingConsultation($rec, $request) {
        if (isset($request->clientid) || isset($request->clientsecret) || isset($request->youtube_api_key) || isset($request->youtube_channel_key)) {
            $data = [
                'setting_key'   => 'youtube_api_setting',
                'setting_value' => json_encode([
                    'clientid'            => isset($request->clientid) ? $request->clientid : null,
                    'clientsecret'        => isset($request->clientsecret) ? $request->clientsecret : null,
                    'youtube_api_key'     => isset($request->youtube_api_key) ? $request->youtube_api_key : null,
                    'youtube_channel_key' => isset($request->youtube_channel_key) ? $request->youtube_channel_key : null,
                ]),
            ];
            $key = 'youtube_api_setting';
            $this->setSettingTable($key, $data);
        }
        return checkValSet($request->consultation_enable);
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the default account settings for bulk account creation
     * This will set the default settings for the account as requirements.
     * -----------------------------------------------------------------------------------------------------------------
     * @param $accountId
     * @return array
     */
    public function prepareAccSettingForBulk($accountId) {
        return [
            'account_id'                => $accountId,
            
            // test version section
            'test_version'              => 1,
            
            // main configuration
            'light_version'             => 0,
            'mobile_enable'             => 1,
            'multiLoginEnabled'         => 1, // nested mobile enable
            'email_enabled'             => 1,
            'travel_enable'             => 1,
            
            // modules configuration
            'user_group_enable'         => 1,
            'wvm_enable'                => 1, // workshop video meeting, commented in settings
            'fvm_enable'                => 1, // flash video meeting, commented in settings
            'wiki_enable'               => 0,
            'reminder_enable'           => 0,
            'zip_download'              => 1,
            'fts_enable'                => 0, // REMOVED
            'repd_connect_mode'         => 1,
            'prepd_repd_notes'          => 1,
            'project_enable'            => 0,
            'custom_profile_enable'     => 1,
            'meeting_meal_enable'       => 1,
            'notes_to_secretary_enable' => 1,
            'import_enable'             => 0,
            
            
            // future modules
            'survey_menu_enable'        => 0,
            'elearning_menu_enabled'    => 0,
            'crm_menu_enable'           => 0,
            'reseau_menu_enable'        => 0,
            'wiki_menu_enable'          => 0,
            'piloter_menu_enable'       => 0,
            
            // these keys are present in account table but there is no option to set them from account setting page.
            'display_internal_id'       => 1, // todo check from removed and ask default
            'new_member_enabled'        => 1, // todo check from removed and ask default
            'new_member_alert'          => 1, // todo check from removed and ask default
            'newsletter_menu_enable'    => 1, // todo ask client
            'display_prof_data'         => 1, // todo ask client
            
            'setting' => json_encode($this->prepareSettingForBulk()),
        
        ];
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the data for the setting column of account_setting table.
     * This will contain the default setting when a new account is created
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function prepareSettingForBulk() {
        return [
            // icontact information
            "ICONTACT_API_APP_ID"         => "",
            "ICONTACT_API_PASSWORD"       => "",
            "ICONTACT_API_USERNAME"       => "",
            "ICONTACT_CLIENT_FOLDER_ID"   => "",
            "ICONTACT_ACCOUNT_ID"         => "",
            
            // Photo Stock Configuration
            "stock_setting"               => [
                "enabled"          => 0,
                "max_allowed"      => "0",
                "available_credit" => "0",
                "renewal_date"     => "1"
                // key , app name storing in settings table.
            ],
            
            // Event Settings
            "event_enabled"               => 0,
            "event_settings"              => [
                "wp_enabled"          => 0,
                // wp keys are stored in settings table
                "bluejeans_enabled"   => 0,
                // bluejeans keys are stored in setting table
                "keep_contact_enable" => 0,
                // kct fields are stored in settings table.
            ],
            
            // Transcribe Setting
            "transcribe_setting"          => [
                "enabled"          => 0,
                "max_allowed"      => 1,
                "available_credit" => 1,
            ],
            
            // Module Configuration, some are stored in account_settings main columns
            "crm_enable"                  => 0,
            "instance_enable"             => 0,
            "press_enable"                => 0,
            "news_letter_enable"          => 0,
            "manage_template"             => 0, // nested news_letter_enable
            "organiser_setting_enable"    => 1,
            "messenger_enable"            => 1,
            "workshop_graphic_enable"     => 1,
            "qualification_enable"        => 0,
            "video_meeting_enable"        => 1,
            "direct_video_enable"         => 0,
            "consultation_enable"         => 1,
            "reinvent_enable"             => 0, // nested consultation
            
            // Future Modules
            "workshops_enable"            => 1,
            "documents_enable"            => 1,
            
            // Vertical Bar
            "vertical_bar_enable"         => 0,
            "add_module_enable"           => 0,
            "vertical_messenger_enable"   => 0,
            "vertical_news_letter_enable" => 0,
            "vertical_event_enabled"      => 0,
            "feature_request_enable"      => 0,
            "help_enable"                 => 0,
            "share_enable"                => 0,
            "others_enable"               => 0,
            // removed settings
            // these settings are removed but still have existence in backend so putting some value
            "news_moderation_enable"      => 0,
            "label_tooltip_show"          => 1, // todo ask client what to put as not present in account setting
        ];
    }
}
