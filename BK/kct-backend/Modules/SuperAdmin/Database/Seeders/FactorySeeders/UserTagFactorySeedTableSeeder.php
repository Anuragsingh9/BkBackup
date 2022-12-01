<?php

namespace Modules\SuperAdmin\Database\Seeders\FactorySeeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\SuperAdmin\Entities\UserTag;

class UserTagFactorySeedTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Model::unguard();
        UserTag::factory()->count(100)->create();
    }
}
