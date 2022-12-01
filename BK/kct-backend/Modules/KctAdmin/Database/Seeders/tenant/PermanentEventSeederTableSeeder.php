<?php

namespace Modules\KctAdmin\Database\Seeders\tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\KctAdmin\Exceptions\DefaultGroupNotFoundException;
use Modules\KctAdmin\Traits\ServicesAndRepo;

class PermanentEventSeederTableSeeder extends Seeder {
    use ServicesAndRepo;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Model::unguard();
        try {
            $this->adminServices()->eventService->createWaterFountainEvent();
        } catch(DefaultGroupNotFoundException $e) {
            // do nothing here as on account creation this will be thrown for the first time
            // as after seeders the default group will be created so ignoring the error here
        }
    }
}
