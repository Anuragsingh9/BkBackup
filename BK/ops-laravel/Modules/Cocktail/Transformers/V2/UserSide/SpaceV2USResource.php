<?php

namespace Modules\Cocktail\Transformers\V2\UserSide;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

class SpaceV2USResource extends Resource {
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
        return [
            'space_uuid'       => $this->resource->space_uuid,
            // currently using it as space line 1
            'space_name'       => $this->resource->space_name,
            // currently using it as space line 2
            'space_short_name' => $this->resource->space_short_name,
            'space_mood'       => $this->resource->space_mood,
            'max_capacity'     => $this->resource->max_capacity,
            'is_vip_space'     => $this->resource->is_vip_space,
            'is_duo_space'     => $this->resource->is_duo_space,
            'space_type'       => $spaceType,
            'event_uuid'       => $this->event_uuid,
            'users_count'      => 0, // front need this key and then its value will be handled via socket
        ];
    }
}
