<?php

namespace Modules\KctAdmin\Transformers\V4;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\KctAdmin\Transformers\V1\UserMinResource;

/**
 * @OA\Schema(
 *  title="Resource: SpaceResource",
 *  description="Space Resource",
 *  @OA\Property(property="space_uuid",type="uuid",description="Unique UUID of Space",
 *     example="123e4567-e89b-12d3-a456-426614174000"
 *  ),
 *  @OA\Property(property="max_capacity",type="integer",description="Maximum Capacity for space",example="1"),
 *  @OA\Property(property="is_default",type="integer",description="To check if space is default space",example="1"),
 *  @OA\Property(property="event_uuid",type="uuid",description="UUID of Event",
 *     example="123e4567-e89b-12d3-a456-426614174000"
 *  ),
 *  @OA\Property(property="host",type="array",description="Space hosts",
 *     @OA\Items(ref="#/components/schemas/UserMinResource")
 *  ),
 * ),
 *
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will manage the resource of the space
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class SpaceV4Resource
 * @package Modules\KctAdmin\Transformers\V4
 */
class SpaceV4Resource extends JsonResource {
    use ServicesAndRepo;

    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {

        return [
            'space_uuid'   => $this->resource->space_uuid,
            'space_host'   => new UserMinResource($this->resource->spaceHost->user),
            'space_max_capacity' => $this->resource->max_capacity,
            'space_is_default'   => (int)($this->resource->event->spaces->first()->space_uuid
                == $this->resource->space_uuid),
            'space_is_vip' => (boolean) $this->resource->is_vip_space,
            'space_line_1' => $this->resource->space_name,
            'space_line_2' => $this->resource->space_short_name,
        ];
    }
}
