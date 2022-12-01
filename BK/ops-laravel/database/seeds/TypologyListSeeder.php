<?php

use Illuminate\Database\Seeder;

class TypologyListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $typo=[[
            'name'=>'NewsletterList',
        ],
        [
            'name'=>'SurveyList',
        ]];
        foreach ($typo as $key => $value) {
        DB::connection('tenant')->table('newsletter_typology')->updateOrInsert(['name' => $value['name']],$value);
        }
    }
}
