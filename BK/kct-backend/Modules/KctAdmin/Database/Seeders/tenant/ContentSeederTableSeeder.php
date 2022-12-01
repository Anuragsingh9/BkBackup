<?php

namespace Modules\KctAdmin\Database\Seeders\tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\KctAdmin\Entities\Group;
use Modules\KctAdmin\Traits\ServicesAndRepo;

class ContentSeederTableSeeder extends Seeder {
    use ServicesAndRepo;

    private function syncSetting($groupId, $key, $value) {

        $setting = $this->adminRepo()->settingRepository->getSettingByKey($key, $groupId);
        if (!$setting) {
            $this->adminServices()->groupService->syncGroupSettings($groupId);
            $setting = $this->adminRepo()->settingRepository->getSettingByKey($key, $groupId);
        }

        if (!$setting) {
            return;
        }

        if (isset($setting['setting_value']) && $setting['setting_value'][$key] == null) {
            $imagePath = $value;
            $settingValue = $setting['setting_value'];
            $settingValue[$key] = $imagePath;
            $setting['setting_value'] = $settingValue;
            $setting->update();
        }

    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Model::unguard();
        $groups = Group::all();
        $userGridImage = $this->adminServices()->superAdminService->getUserGridImage();;
        foreach ($groups as $group) {
            $this->syncSetting(
                $group->id,
                'event_image' ,
                config('kctadmin.constants.event_default_image_path')
            );

            $this->syncSetting(
                $group->id,
                'video_explainer_alternative_image',
                $userGridImage
            );
        }
    }
}
