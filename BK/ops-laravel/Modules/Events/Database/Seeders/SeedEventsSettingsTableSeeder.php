<?php

namespace Modules\Events\Database\Seeders;

use App\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class SeedEventsSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $exclude = [];
        $data = [
            [
                "setting_key" => "events_default_organiser",
                "setting_value" => ''
            ],
            [
                "setting_key" => "events_prefix",
                "setting_value" => ''
            ],
        ];

        foreach ($data as $key => $value) {
            if (!in_array($value['setting_key'], $exclude)) {
                Setting::updateOrCreate(['setting_key' => $value['setting_key']], $value);
            }
        }
    }
}
