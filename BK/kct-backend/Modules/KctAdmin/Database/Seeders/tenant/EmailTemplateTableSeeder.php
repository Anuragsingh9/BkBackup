<?php

namespace Modules\KctAdmin\Database\Seeders\tenant;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\KctAdmin\Entities\GroupSetting;

class EmailTemplateTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Model::unguard();

        $defaultTemplate = [
            'email_subject'    => 'email_subject',
            'text_before_link' => 'text_before_link\n',
            'text_after_link'  => 'text_after_link\n',
        ];
        // these keys will auto added with locale in end
        // eg. event_register will have entry if two locale present -> en (event_register_en), fr(event_register_fr)
        $oneTime = [
            // user otp code template
            [
                'setting_key'   => 'event_validation_code',
                'setting_value' => array_merge($defaultTemplate, ['link' => config('kctadmin.constants.emailLinks.EVENT_LOGIN')]),
            ],
        ];
        $langs = array_keys(config("kctadmin.moduleLanguages"));
        foreach ($oneTime as $email) {
            foreach ($langs as $lang) {
                $key = "{$email['setting_key']}_$lang";
                $setting = GroupSetting::where('setting_key', $key)->first();
                if ($setting) {
                    continue;
                }
                $data = $email;
                $data['setting_key'] =  $key;
                GroupSetting::create($data);
            }
        }
    }
}



