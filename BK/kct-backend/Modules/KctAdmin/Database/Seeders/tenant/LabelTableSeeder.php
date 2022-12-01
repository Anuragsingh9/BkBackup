<?php

namespace Modules\KctAdmin\Database\Seeders\tenant;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\KctAdmin\Entities\Group;
use Modules\KctAdmin\Entities\GroupSetting;
use Modules\KctAdmin\Entities\Label;
use Modules\KctAdmin\Entities\LabelLocale;
use Modules\KctAdmin\Traits\ServicesAndRepo;

class LabelTableSeeder extends Seeder {
    use ServicesAndRepo;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Model::unguard();
        $groups = Group::all();
        foreach ($groups as $group) {
            $this->adminServices()->groupService->syncLabels($group);
        }
    }
}



