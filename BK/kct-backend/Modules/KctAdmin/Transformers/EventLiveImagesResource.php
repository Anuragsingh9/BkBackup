<?php

namespace Modules\KctAdmin\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\SuperAdmin\Traits\ServicesAndRepo;
/**
 * @OA\Schema(
 *  title="APIResource: EventLiveImagesResource",
 *  description="This resource contains events which are in invitation plan phase ",
 *  @OA\Property( property="key",type="string",description="The unique key or each asset",example="0bd13a00-14a0-11ec-a4fa-74867a0dc41b"),
 *  @OA\Property( property="value",type="string",description="URL of the asset",example="http://asset.com"),
 *  @OA\Property( property="thumbnail_path",type="string",description="Thumbnail URL of the asset",example="http://asset_thumb.com"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will return all data related to Event live Images
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EventLiveImagesResource
 *
 * @package Modules\KctAdmin\Transformers
 */
class EventLiveImagesResource extends JsonResource {
    use ServicesAndRepo;

    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        $imagePath = $this->suServices()->fileService->getFileUrl($this->resource['path']);
        $thumbnailPath = $this->suServices()->fileService->getFileUrl($this->resource['thumbnail_path']);
        return [
            'key'            => $this->resource['key'],
            'value'          => $imagePath,
            'thumbnail_path' => $thumbnailPath,
        ];
    }
}
