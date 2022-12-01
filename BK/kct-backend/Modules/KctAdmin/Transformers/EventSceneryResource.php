<?php

namespace Modules\KctAdmin\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctAdmin\Transformers\V1\LabelLocaleResource;
use Modules\SuperAdmin\Entities\SceneryAsset;

/**
 * @OA\Schema(
 *  title="Resource: EventSceneryResource",
 *  description="Event scenery Resource",
 *
 *  @OA\Property(property="category_id",type="category id",description="Unique ID of category",example="12"),
 *  @OA\Property(property="category_name",type="string",description="Name of category",example="Category Name"),
 *  @OA\Property(property="category_assets",type="array",description="Scenery asset resource",
 *     @OA\Items(ref="#/components/schemas/SceneryAssetResource")
 *  ),
 *  @OA\Property(property="category_locales",type="array",description="Label local resource",
 *     @OA\Items(ref="#/components/schemas/LabelLocaleResource")
 *  ),
 *  @OA\Property(property="category_type",type="string",description="Type of category",example="cafeteria"),
 * ),
 *
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will manage the resource of event scenery resource
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class EventSceneryResource
 * @package Modules\KctAdmin\Transformers
 */
class EventSceneryResource extends JsonResource {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Transform the resource into an array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        $categoryType = $this->resource->asset->first()
            ? ($this->resource->asset->first()->asset_type)
            : null;
        return [
            'category_id'      => $this->resource->id,
            'category_name'    => $this->resource->name,
            'category_assets'  => SceneryAssetResource::collection($this->resource->asset),
            'category_locales' => LabelLocaleResource::collection($this->resource->sceneryLocale),
            'category_type'    => $categoryType,
        ];
    }
}
