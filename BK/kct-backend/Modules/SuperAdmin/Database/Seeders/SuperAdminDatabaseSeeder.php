<?php

namespace Modules\SuperAdmin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class SuperAdminDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(FirstSuperAdminTableSeeder::class);
        $this->call(SettingTableSeeder::class);
        $this->call(S3ImageTransferTableSeeder::class);
        $this->call(DemoLiveAssetTableSeeder::class);
        $this->call(UploadSceneryThumbnailSeeder::class);
    }
}
