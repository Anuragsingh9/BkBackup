<?php


namespace Modules\Events\Service;


use App\Services\Service;
use App\Setting;
use App\User;
use Modules\Cocktail\Exceptions\CustomValidationException;

class OrganiserService extends Service {
    
    /**
     * @return User
     * @throws CustomValidationException
     */
    public function getDefaultOrganiser($type) {
        $setting = $this->getEventSetting();
        // if the event type is internal then return the internal event default organiser
        $defaultOrganiser = false;
        // this if else to get data from different field of setting with proper isset
        if ($type == 'int') {
            $defaultOrganiser = (isset($setting['event_org_setting']['default_organiser']))
                ? $setting['event_org_setting']['default_organiser']
                : null;
        } else if ($type == 'virtual') { // type is virtual
            $defaultOrganiser = (isset($setting['event_virtual_org_setting']['default_organiser']))
                ? $setting['event_virtual_org_setting']['default_organiser']
                : null;
        }
        if ($defaultOrganiser === null) {
            throw new CustomValidationException(__('message.organiserNotSet'));
        } else if ($defaultOrganiser === false) {
            // that means trying to get the default organiser for other type
            // for that the default organiser is not yet defined
            return null;
        }
        $user = User::find($defaultOrganiser);
        if (!$user) {
            throw new CustomValidationException(__('validation.exists', ['attribute' => 'Organiser']));
        }
        return $user;
    }

    /**
     * To get the default organiser for the event of physical internal type
     *
     * @return User
     * @throws CustomValidationException
     */
    public function getEventSetting() {
        $setting = Setting::where('setting_key', 'event_settings')->first();
        if (!$setting) {
            throw new CustomValidationException(__('message.organiserNotSet'));
        }
        return json_decode($setting->setting_value, JSON_OBJECT_AS_ARRAY);
    }
 
    /**
     * @param string $type
     * @return mixed
     * @throws CustomValidationException
     */
    public function getDefaultPrefix($type) {
        $setting = Setting::where('setting_key', 'event_settings')->first();
        if (!$setting) {
            throw new CustomValidationException(__('message.organiserNotSet'));
        }
        $settingDecoded = json_decode($setting->setting_value);
        if ($type == 'virtual') {
            if (!isset($settingDecoded->event_virtual_org_setting->prefix))
                throw new CustomValidationException(__('message.organiserNotSet'));
            return $settingDecoded->event_virtual_org_setting->prefix;
        }
        if (!isset($settingDecoded->event_org_setting->prefix))
            throw new CustomValidationException(__('message.organiserNotSet'));
        return $settingDecoded->event_org_setting->prefix;
    }
}
