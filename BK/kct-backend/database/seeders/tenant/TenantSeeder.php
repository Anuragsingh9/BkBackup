<?php

namespace Database\Seeders\tenant;

use Illuminate\Database\Seeder;
use Nwidart\Modules\Facades\Module;

class TenantSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $modules = Module::all();
        foreach($modules as $module => $data) {
            if(class_exists("Modules\\$module\\Database\\Seeders\\tenant\\TenantDatabaseSeeder")) {
                $this->call("Modules\\$module\\Database\\Seeders\\tenant\\TenantDatabaseSeeder");
            }
        }
    }
}
