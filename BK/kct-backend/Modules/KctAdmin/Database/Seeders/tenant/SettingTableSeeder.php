<?php

namespace Modules\KctAdmin\Database\Seeders\tenant;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\KctAdmin\Entities\Group;
use Modules\KctAdmin\Entities\GroupSetting;
use Modules\KctAdmin\Traits\ServicesAndRepo;

class SettingTableSeeder extends Seeder
{    use ServicesAndRepo;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $oneTime = [
            // user otp code template
            [
                'setting_key'   => 'email_graphics',
                'setting_value' => [
                    'headerLogo' => '',
                    'footerLogo' => '',
                ],
            ],
        ];
        foreach ($oneTime as $data) {
            $setting = GroupSetting::where('setting_key', $data['setting_key'])->first();
            if ($setting) {
                continue;
            }
            GroupSetting::create($data);

        }
        // keys for allow sub-groups different kinds of features
        $manageGroupSettingsKeys = ['allow_manage_group_user', 'allow_manage_group_pilot_and_owner', 'allow_manage_group_design_setting'];
        foreach ($manageGroupSettingsKeys as $settingKey) {
            $keyExists = GroupSetting::where('setting_key', $settingKey)->first();
            if ($keyExists) {
                continue;
            }
            GroupSetting::create(['setting_key' => $settingKey, 'setting_value' => ["$settingKey" => 0]]);
        }

        //If account setting keys not added during account creation, adding keys through seeder.
        $accountSettings = config('superadmin.constants.setting_keys.account_settings');
        $setting = GroupSetting::where('setting_key', 'account_settings')->first();
        if (!$setting) {
            GroupSetting::create(['setting_key' => 'account_settings', 'setting_value' => $accountSettings]);
        } else {
            $settingValue = $setting->setting_value;
            $settingKeys = array_keys($settingValue);
            foreach($accountSettings as $key => $accSetting) {
                if(!in_array($key, $settingKeys)) {
                    $settingValue[$key] = $accSetting;
                }
            }
            $setting->setting_value = $settingValue;
            $setting->update();
        }



        // Synchronize the main setting of groups
        $this->adminRepo()->groupRepository->syncGroupMainSetting();

    }
}



