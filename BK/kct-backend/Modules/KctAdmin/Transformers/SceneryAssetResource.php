<?php

namespace Modules\KctAdmin\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Intervention\Image\Facades\Image;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\SuperAdmin\Entities\SceneryAsset;

/**
 * @OA\Schema(
 *  title="Resource: SceneryAssetResource",
 *  description="Scenery asset Resource",
 *
 *  @OA\Property(property="asset_id",type="Asset id",description="ID of asset",example="12"),
 *  @OA\Property(property="asset_path",type="string",description="Path of the image and video",
 *     example="https://s3.eu-west-2.amazonaws.com/kct-dev/general/group_logo/default.png"
 *  ),
 *  @OA\Property(property="asset_thumbnail_path",type="string",description="Thumbnail path of the image and video",
 *     example="https://s3.eu-west-2.amazonaws.com/kct-dev/general/group_logo/default.png"
 *  ),
 *  @OA\Property(property="asset_default_color",type="string",description="asset default color",example="oranage color"),
 * ),
 *
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will manage the resource of event scenery resource
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class EventSceneryResource
 * @package Modules\KctAdmin\Transformers
 */
class SceneryAssetResource extends JsonResource {
    use ServicesAndRepo;
    use KctHelper;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Transform the resource into an array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        $defaultTopBg = config('kctadmin.scenery.top_bg_color');
        $thumbnailPath = null;
        if ($this->resource->asset_path && $this->resource->asset_path != null){
            $pathInfo = pathinfo($this->resource->asset_path);
            // appending _thumb as suffix in the asset path so that thumbnail can be fetched which is uploaded with
            // _thumb as suffix in asset name.
            $thumbnailPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumb' . '.' . $pathInfo['extension'];
        }
        return [
            'asset_id'             => $this->resource->id,
            'asset_path'           => $this->resource->asset_path
                ? $this->adminServices()->fileService->getFileUrl($this->resource->asset_path)
                : null,
            'asset_thumbnail_path' => $thumbnailPath
                ? $this->adminServices()->fileService->getFileUrl($thumbnailPath)
                : null,
            'asset_default_color'  => $this->hexToRgba($this->resource->asset_settings['color'] ?? $defaultTopBg)
        ];
    }
}
