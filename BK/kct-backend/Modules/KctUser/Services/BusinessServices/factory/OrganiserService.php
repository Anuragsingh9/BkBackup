<?php


namespace Modules\KctUser\Services\BusinessServices\factory;

use App\Services\Service;
use App\Setting;
use App\User;
use Modules\Cocktail\Exceptions\CustomValidationException;
use Modules\KctUser\Services\BusinessServices\IOrganiserService;

class OrganiserService implements IOrganiserService {

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
}
