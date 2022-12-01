<?php

use Illuminate\Database\Seeder;

class GuideSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $default = config('constants.defaults.s3');
        $data = [
            [
                "title_en"  => $default['notification-allow-guide-EN']['name'],
                "title_fr"  => $default['notification-allow-guide-FR']['name'],
                "upload_en" => $default['notification-allow-guide-EN']['path'],
                "upload_fr" => $default['notification-allow-guide-FR']['path'],
                "role"      => 2, // anyone can access
            ],
        ];
        foreach ($data as $row) {
            $g = DB::connection('mysql')->table('guides')
                ->where('title_en', $row['title_en'])
                ->where('title_fr', $row['title_fr'])
                ->first();
            if (!$g) {
                DB::connection('mysql')->table('guides')
                    ->insert($row);
            }
        }
    }
}
