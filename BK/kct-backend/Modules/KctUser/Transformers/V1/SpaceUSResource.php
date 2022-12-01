<?php

namespace Modules\KctUser\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *  title="APIResource: EventResource",
 *  description="Physical Event Resource",
 *  @OA\Property(property="space_uuid",type="uuid",description="Unique UUID of Space",example="123e4567-e89b-12d3-a456-426614174000"),
 *  @OA\Property(property="space_name",type="string",description="Space Name",example="Space Name"),
 *  @OA\Property(property="space_short_name",type="string",description="Space Short Name",example="Space Short Name"),
 *  @OA\Property(property="space_mood",type="string",description="Space Mood Name",example="Space Mood Name"),
 *  @OA\Property(property="max_capacity",type="integer",description="Max Capacity of Space",example="Max Capacity"),
 *  @OA\Property(property="is_vip_space",type="integer",description="To indicate if space is vip",example="0"),
 *  @OA\Property(property="is_mono_space",type="integer",description="To indicate if space is mono",example="0"),
 *  @OA\Property(property="space_type",type="integer",description="To indicate if space type",example="1"),
 *  @OA\Property(property="event_uuid",type="UUID",description="Event Uuid",example="01493146-d018-11ea-9d2a-b82a72a009b4"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will return all data related to a space.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class SpaceUSResource
 *
 * @package Modules\KctUser\Transformers\V1
 */
class SpaceUSResource extends JsonResource {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Transform the resource into an array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        $spaceType = $this->resource->is_vip_space == 1
            ? config('kctuser.default.space_type_vip')
            : ($this->resource->is_duo_space == 1
                ? config('kctuser.default.space_type_duo')
                : 0);
        return [
            'space_uuid'       => $this->resource->space_uuid,
            // currently, using it as space line 1
            'space_name'       => $this->resource->space_name,
            // currently, using it as space line 2
            'space_short_name' => $this->resource->space_short_name,
            'space_mood'       => $this->resource->space_mood,
            'max_capacity'     => $this->resource->max_capacity,
            'is_vip_space'     => $this->resource->is_vip_space,
            'space_type'       => $spaceType,
            'event_uuid'       => $this->resource->event_uuid,
            'users_count'      => 0, // front need this key and then its value will be handled via socket
            'space_hosts'      =>
                $this->whenLoaded(
                    'hosts',
                    HostUSResource::collection($this->resource->relationLoaded('hosts') ? $this->resource->hosts : collect([]))
                ),
            'is_mono_space'    => $this->resource->is_mono_space ? 1 : 0,
        ];
    }
}
