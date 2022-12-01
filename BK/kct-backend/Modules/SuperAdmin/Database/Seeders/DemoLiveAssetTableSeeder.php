<?php

namespace Modules\SuperAdmin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\SuperAdmin\Entities\DemoLiveAsset;

class DemoLiveAssetTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Model::unguard();
        // Note: Here different asset types are:- 1. YouTube video link 2. Vimeo video link 3. Image
        $assets = [
            [
                'asset_path' => 'https://www.youtube.com/watch?v=cx8j_DzmOOg',
                'asset_type' => 1,
                'category'   => 'default'
            ], [
                'asset_path' => 'https://www.youtube.com/watch?v=WibjzL3fF1o',
                'asset_type' => 1,
                'category'   => 'default'
            ], [
                'asset_path' => 'https://www.youtube.com/watch?v=D0UnqGm_miA',
                'asset_type' => 1,
                'category'   => 'default'
            ], [
                'asset_path' => 'https://www.youtube.com/watch?v=tpdUUxgh6Q0',
                'asset_type' => 1,
                'category'   => 'default'
            ], [
                'asset_path' => 'https://www.youtube.com/watch?v=Fmw-F4r5sc8',
                'asset_type' => 1,
                'category'   => 'default'
            ], [
                'asset_path' => 'https://www.youtube.com/watch?v=vVEV5J5S13g',
                'asset_type' => 1,
                'category'   => 'default'
            ], [
                'asset_path' => 'https://www.youtube.com/watch?v=xWaTjYpczKQ',
                'asset_type' => 1,
                'category'   => 'default'
            ], [
                'asset_path' => 'https://www.youtube.com/watch?v=iT51M99eBoI&t=197s',
                'asset_type' => 1,
                'category'   => 'default'
            ]
        ];
        foreach ($assets as $asset) {
            $assetExist = DemoLiveAsset::whereAssetPath($asset['asset_path'])->first();
            if ($assetExist) {
                continue;
            }
            DemoLiveAsset::create($asset);
        }
        // $this->call("OthersTableSeeder");
    }
}
