<?php

namespace Modules\SuperAdmin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\SuperAdmin\Entities\Setting;

class SettingTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Model::unguard();
        $data = [
            [
                'setting_key'   => 'public_video',
                'setting_value' => [
                    'video_explainer_enabled' => 1,
                    'display_on_reg'          => 1,
                    'display_on_live'         => 1,
                    'public_video_en'         => 'https://www.youtube.com/watch?v=VnyitUU4DUY',
                    'public_video_fr'         => 'https://www.youtube.com/watch?v=VnyitUU4DUY',
                    'image_path'              => 'assets/default-video-explainer-alt.png'
                ],
            ],
        ];

        foreach ($data as $d) {
            $setting = Setting::firstOrCreate([
                'setting_key' => $d['setting_key'],
            ], [
                'setting_value' => $d['setting_value'],
            ]);
            $settingValues = $setting->setting_value;
            $svKeys = array_keys($settingValues);
            foreach($d['setting_value'] as $definedKeys => $value) {
                if(!in_array($definedKeys, $svKeys)) {
                    $settingValues[$definedKeys] = $value;
                }
            }
            $setting->setting_value = $settingValues;
            $setting->update();
        }
    }
}
