<?php

namespace Modules\KctAdmin\Database\Seeders\tenant;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\KctAdmin\Traits\ServicesAndRepo;

class AccountSettingSeederTableSeeder extends Seeder
{   use ServicesAndRepo;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $accountSetting = $this->adminRepo()->groupRepository->getAccountSettings();
        $settingValue = $accountSetting->setting_value;
        $settingValue['events_enabled'] = $settingValue['events_enabled'] ?? 1;
        $settingValue['kct_enabled']    = $settingValue['kct_enabled'] ?? 1;
        $settingValue['conference_enabled'] = $settingValue['conference_enabled'] ?? 1;
        $settingValue['allow_multi_group'] = $settingValue['allow_multi_group'] ?? 0;
        $settingValue['max_group_limit'] = $settingValue['max_group_limit'] ?? 0;
        $settingValue['allow_user_to_group_creation'] = $settingValue['allow_user_to_group_creation'] ?? 0;
        $settingValue['group_analytics'] = $settingValue['group_analytics'] ?? 0;
        $settingValue['event_analytics'] = $settingValue['event_analytics'] ?? 0;
        $settingValue['acc_analytics'] = $settingValue['acc_analytics'] ?? 0;
        $accountSetting->setting_value = $settingValue;
        $accountSetting->update();

    }
}
