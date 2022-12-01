<?php

namespace Modules\KctAdmin\Database\Seeders\tenant;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\KctAdmin\Entities\MomentType;

class ContentTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $contentTypeNames = ['networking','default_zoom_webinar','zoom_webinar','zoom_meeting','youtube_pre_recorded','vimeo_pre_recorded'];
        foreach ($contentTypeNames as $name){
            $exist = MomentType::where('name',$name)->first();
            if ($exist){
                continue;
            }
            MomentType::create(['name' => $name]);
        }
        // $this->call("OthersTableSeeder");
    }
}
