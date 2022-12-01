<?php

namespace Modules\Cocktail\Transformers\V2\UserSide\Queue;

use Illuminate\Http\Resources\Json\Resource;
use Modules\Cocktail\Transformers\V2\UserSide\BadgeV2USResource;

class IndexResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        // list1 -> "User missed calls from other"
        // list2 -> "Others missed calls from user"
        $result = [
            'list1'          => $this->resource->i_missed ? BadgeV2USResource::collection($this->resource->i_missed) : null,
            'list2'          => $this->resource->i_missed ? BadgeV2USResource::collection($this->resource->they_missed) : null,
        ];
        return $result;
    }
}
