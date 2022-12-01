<?php

namespace Modules\UserManagement\Database\Seeders\tenant;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\UserManagement\Entities\EntityType;

class EntityTypeTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Model::unguard();
        if (EntityType::count() == 0) {
            EntityType::create([
                'name'  => "Company",
                'level' => 1,
            ]);
            EntityType::create([
                'name'  => "Union",
                'level' => 1,
            ]);
        }
    }
}



