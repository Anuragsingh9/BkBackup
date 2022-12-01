<?php

use Illuminate\Database\Seeder;

class ActivityOverdueAlert extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            'setting_key'=>'overdue_alert',
            'setting_value'=>'{"color":"#ff0000"}'
        ]);
    }
}
