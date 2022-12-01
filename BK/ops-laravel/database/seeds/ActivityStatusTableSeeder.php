<?php

use Illuminate\Database\Seeder;
use App\Model\ActivityStatus;
class ActivityStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data=[[
            'en_label' => 'Planned',
            'fr_label' => 'À faire',
            'status' => '1'
        ], [
            'en_label' => 'Working on it',
            'fr_label' => 'En cours',
            'status' => '2'
        ], [
            'en_label' => 'Done',
            'fr_label' => 'Terminé',
            'status' => '3'
        ],
        ];
       foreach ($data as $key => $value) {
            ActivityStatus::updateOrCreate(['status'=>$value['status']],$value);
        }
    }
}
