<?php

namespace Modules\UserManagement\Database\Seeders\tenant;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class TenantDatabaseSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Model::unguard();
        $this->call(RoleAndPermissionTableSeeder::class);
        $this->call(EntityTypeTableSeeder::class);
    }
}
