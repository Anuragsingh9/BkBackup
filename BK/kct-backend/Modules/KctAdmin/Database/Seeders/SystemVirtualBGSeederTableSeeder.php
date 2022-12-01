<?php

namespace Modules\KctAdmin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\KctUser\Entities\SystemVirtualBackgrounds;

class SystemVirtualBGSeederTableSeeder extends Seeder {
    use ServicesAndRepo;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Model::unguard();
        $images = SystemVirtualBackgrounds::all();
        if (count($images) === 0) {
            $data = $this->adminServices()->superAdminService->getAllSceneryData();
            foreach ($data as $d => $values) {
                if ($d > 0) {
                    foreach ($values->asset as $value) {
                        $backgroundImages = [
                            'image_url' => $value->asset_path,
                            'bg_type'   => 1,
                        ];
                        SystemVirtualBackgrounds::create($backgroundImages);
                    }
                }
            }

        }
        // $this->call("OthersTableSeeder");
    }
}
