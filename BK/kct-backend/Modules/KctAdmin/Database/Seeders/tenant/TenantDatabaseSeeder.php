<?php

namespace Modules\KctAdmin\Database\Seeders\tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Model::unguard();
        $this->call(SettingTableSeeder::class);
        $this->call(EmailTemplateTableSeeder::class);
        $this->call(ContentTypesTableSeeder::class);
        $this->call(LabelTableSeeder::class);
        $this->call(ContentSeederTableSeeder::class);
        $this->call(GroupTypesTableSeeder::class);
        $this->call(MigrationVersion3Seeder::class);
        $this->call(AccountSettingSeederTableSeeder::class);
        $this->call(MigrationVersion4Seeder::class);
        $this->call(PermanentEventSeederTableSeeder::class);
    }
}
