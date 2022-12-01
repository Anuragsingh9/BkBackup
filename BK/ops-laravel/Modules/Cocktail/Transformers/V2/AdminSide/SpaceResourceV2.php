<?php

namespace Modules\Cocktail\Transformers\V2\AdminSide;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

/**
 * @OA\Schema(
 *  title="APIResource: SpaceResourceV2",
 *  description="Space Resource",
 *
 *  @OA\Property(property="space_uuid",type="uuid",description="Unique UUID of Space",example="123e4567-e89b-12d3-a456-426614174000"),
 *  @OA\Property(property="space_name",type="string",description="Name of space",example="Space Name"),
 *  @OA\Property(property="space_short_name",type="string",description="Space Short Name",example="Space Short Name"),
 *  @OA\Property(property="space_mood",type="string",description="Space Mood",example="Space Mood"),
 *  @OA\Property(property="max_capacity",type="integer",description="Maximum Capacity for space",example="1"),
 *  @OA\Property(property="is_vip_space",type="integer",description="To indicate if space is vip",example="1", enum={"0", "1"}),
 *  @OA\Property(property="is_duo_space",type="integer",description="To indicate if space is duo",example="1", enum={"0", "1"}),
 *  @OA\Property(property="event_uuid",type="uuid",description="UUID of Event",example="123e4567-e89b-12d3-a456-426614174000"),
 *  @OA\Property(property="users_count",type="integer",description="Count of users in space",example="1"),
 *  @OA\Property(property="workshop_id",type="integer",description="ID of workshop for space event",example="1"),
 *  @OA\Property(property="order_id",type="string",description="Sorting ID",example="a"),
 *  @OA\Property(property="is_default",type="integer",description="To indicate if space is default space for event",example="1", enum={"0","1"}),
 * ),
 */
class SpaceResourceV2 extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request) {
        $spaceType = $this->resource->is_vip_space == 1
            ? config('cocktail.default.space_type_vip')
            : ($this->resource->is_duo_space == 1
                ? config('cocktail.default.space_type_duo')
                : 0);
        $result = [
            'space_uuid'       => $this->resource->space_uuid,
            'space_name'       => $this->resource->space_name,
            'space_short_name' => $this->resource->space_short_name,
            'space_mood'       => $this->resource->space_mood,
            'max_capacity'     => $this->resource->max_capacity,
            'is_vip_space'     => $this->resource->is_vip_space,
            'is_duo_space'     => $this->resource->is_duo_space,
            'space_type'       => $spaceType,
            'event_uuid'       => $this->event_uuid,
            'users_count'      => isset($this->space_users_count) ? $this->space_users_count : $this->spaceUsers->count(),
            'workshop_id'      => $this->resource->event->workshop_id,
            'order_id'         => $this->resource->order_id,
        ];
        
        if (isset($this->event->defaultSpace->space_uuid) && $this->event->defaultSpace->space_uuid == $this->space_uuid) {
            $result['is_default'] = 1;
        }
        
        return $result;
    }
}
