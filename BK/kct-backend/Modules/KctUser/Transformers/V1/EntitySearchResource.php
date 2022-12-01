<?php

namespace Modules\KctUser\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *  title="APIResource: EntitySearchResource",
 *  description="",
 *  @OA\Property(property="id",type="integer",description="ID of Entity",example="1"),
 *  @OA\Property(property="long_name",type="string",description="Long name of entity",example="hello"),
 *  @OA\Property(property="short_name",type="string",description="Short Name of Entity",example="hello"),
 * )
 *
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will be used for returning the entity data
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class EntitySearchResource
 *
 * @package Modules\KctUser\Transformers\V1
 */
class EntitySearchResource extends JsonResource {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Transform the resource into an array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        return [
            'id'         => $this->resource->id,
            'long_name'  => $this->resource->long_name,
            'short_name' => $this->resource->short_name,
        ];
    }
}
