<?php

namespace Modules\SuperAdmin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;
use Modules\SuperAdmin\Entities\SceneryAsset;
use Modules\SuperAdmin\Traits\ServicesAndRepo;

class UploadSceneryThumbnailSeeder extends Seeder {
    use ServicesAndRepo;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Model::unguard();
        $assetsPath = SceneryAsset::all('asset_path');
        foreach ($assetsPath as $assetPath) {
            if ($assetPath->asset_path) {
                $imagePath = $assetPath->asset_path;
                $imgUrl = $this->suServices()->fileService->getFileUrl($imagePath);
                $imgUrlInfo = pathinfo($imgUrl);
                $originalName = $imgUrlInfo['filename'];
                $filename = $originalName . '_thumb.jpg';
                $uploadPath = "general/scenery/$filename";
                $fileExist = $this->suServices()->fileService->isFileExists($uploadPath);
                if (!$fileExist) { // if file not exist in bucket then resize and upload the file
                    $image = Image::make($imgUrl)->save(public_path("images" . $filename));
                    $image->resize(280, 160)->stream();
                    $uploadPath = "general/scenery/$filename";
                    $this->suServices()->fileService->storeFile($image->__toString(), $uploadPath);
                }
            }
        }

        // $this->call("OthersTableSeeder");
    }
}
