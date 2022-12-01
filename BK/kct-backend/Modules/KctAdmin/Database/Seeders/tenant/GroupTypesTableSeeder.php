<?php

namespace Modules\KctAdmin\Database\Seeders\tenant;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\KctAdmin\Entities\GroupType;

class GroupTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $groupTypeNames = ['super_group','local_group','functional_group','topic_group','head_quarters_group','spontaneous_group','water_fountain_group'];
        foreach ($groupTypeNames as $groupTypeName){
            $exists = GroupType::where('group_type',$groupTypeName)->first();
            if($exists){
               continue;
            }
            GroupType::create(['group_type'=> $groupTypeName]);
        }
        // $this->call("OthersTableSeeder");
    }
}
