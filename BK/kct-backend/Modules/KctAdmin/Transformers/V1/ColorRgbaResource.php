<?php

namespace Modules\KctAdmin\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *  title="APIResource: ColorRgbaResource",
 *  description="This resource contains color values in rgba format",
 *  @OA\Property( property="r",type="integer",description="Red Color Value",example=255),
 *  @OA\Property( property="g",type="integer",description="Green Color Value",example=255),
 *  @OA\Property( property="b",type="integer",description="Blue Color Value",example=255),
 *  @OA\Property( property="a",type="integer",description="Alpha Color Value",example=0.4),
 * )
 */
class ColorRgbaResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        return parent::toArray($request);
    }
}
