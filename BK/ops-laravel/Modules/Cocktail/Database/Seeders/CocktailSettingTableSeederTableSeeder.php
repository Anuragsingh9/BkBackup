<?php

namespace Modules\Cocktail\Database\Seeders;

use App\Setting;
use App\User;
use Illuminate\Database\Seeder;

class CocktailSettingTableSeederTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @check some seeders are present in core setting seeder please check there for also
     *
     * @return void
     */
    public function run() {
        
        $defaultEmailTemplate = '{"email_subject":"email_subject ","text_before_link":"text_before_link\n","text_after_link":"text_after_link\n"}';
        $data = [
            // event registration
            [
                "setting_key"   => "event_kct_registration_EN",
                "setting_value" => $defaultEmailTemplate,
            ], [
                "setting_key"   => "event_kct_registration_FR",
                "setting_value" => $defaultEmailTemplate,
            ],
            // event modification
            [
                "setting_key"   => "event_kct_modification_EN",
                "setting_value" => $defaultEmailTemplate,
            ], [
                "setting_key"   => "event_kct_modification_FR",
                "setting_value" => $defaultEmailTemplate,
            ],
            // internal event reminder 1
            [
                "setting_key"   => "event_int_reminder_1_EN",
                "setting_value" => $defaultEmailTemplate,
            ], [
                "setting_key"   => "event_int_reminder_1_FR",
                "setting_value" => $defaultEmailTemplate,
            ],
            // internal event reminder 2
            [
                "setting_key"   => "event_int_reminder_2_EN",
                "setting_value" => $defaultEmailTemplate,
            ], [
                "setting_key"   => "event_int_reminder_2_FR",
                "setting_value" => $defaultEmailTemplate,
            ],
            // internal event reminder 3
            [
                "setting_key"   => "event_int_reminder_3_EN",
                "setting_value" => $defaultEmailTemplate,
            ], [
                "setting_key"   => "event_int_reminder_3_FR",
                "setting_value" => $defaultEmailTemplate,
            ],
            // KCT scrum2 registration
            [
                "setting_key"   => "event_kct_invite_EN",
                "setting_value" => $defaultEmailTemplate,
            ], [
                "setting_key"   => "event_kct_invite_FR",
                "setting_value" => $defaultEmailTemplate,
            ],
            // KeepContact event reminder 1
            [
                "setting_key"   => "event_kct_reminder_1_EN",
                "setting_value" => $defaultEmailTemplate,
            ], [
                "setting_key"   => "event_kct_reminder_1_FR",
                "setting_value" => $defaultEmailTemplate,
            ],
            // KeepContact event reminder 2
            [
                "setting_key"   => "event_kct_reminder_2_EN",
                "setting_value" => $defaultEmailTemplate,
            ], [
                "setting_key"   => "event_kct_reminder_2_FR",
                "setting_value" => $defaultEmailTemplate,
            ],
            // KeepContact event reminder 3
            [
                "setting_key"   => "event_kct_reminder_3_EN",
                "setting_value" => $defaultEmailTemplate,
            ], [
                "setting_key"   => "event_kct_reminder_3_FR",
                "setting_value" => $defaultEmailTemplate,
            ],
            // Reminder enabled
            [
                "setting_key"   => "event_reminders",
                "setting_value" => '{"reminders":{"event_kct_reminder_1":{"active":0,"days":1},"event_kct_reminder_2":{"active":0,"days":7},"event_kct_reminder_3":{"active":0,"days":14},"event_int_reminder_1":{"active":0,"days":1},"event_int_reminder_2":{"active":0,"days":7},"event_int_reminder_3":{"active":0,"days":14}}}',
            ],
            [
                "setting_key"   => "event_kct_invite_existing_EN",
                "setting_value" => $defaultEmailTemplate,
            ], [
                "setting_key"   => "event_kct_invite_existing_FR",
                "setting_value" => $defaultEmailTemplate,
            ],
        ];
        
        
        $allowedToReplace = [];
        foreach ($data as $key => $value) {
            $exist = Setting::where('setting_key', $value['setting_key']);
            if ($exist->count() == 0) {
                Setting::updateOrCreate(['setting_key' => $value['setting_key']], $value);
            } else {
                $dbVal = $exist->first();
                $decode = json_decode($dbVal->setting_value);
                if (isJson($value['setting_value'])) {
                    $decodeValue = json_decode($value['setting_value']);
                } else {
                    $decodeValue = json_decode(preg_replace('/\s+/', '', $value['setting_value']));
                }
                $decode = collect($decode);
                $newArray = [];
                collect($decodeValue)->each(function ($v, $k) use ($decode, $decodeValue, &$newArray, $allowedToReplace, $value) {
                    
                    if ($decode->has($k)) {
                        if (isset($allowedToReplace[$value['setting_key']]) && in_array($k, $allowedToReplace[$value['setting_key']])) {
                            $newArray[$k] = $v;
                        } else {
                            $newArray[$k] = isset($decode[$k]) ? $decode[$k] : $v;
                        }
                    } else {
                        $newArray[$k] = $v;
                    }
                });
                $exist->update(['setting_value' => json_encode($newArray)]);
            }
        }
    }
}
