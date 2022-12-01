<?php

namespace Modules\Cocktail\Transformers\V2\UserSide;

use Illuminate\Http\Resources\Json\Resource;

class NodeSpaceWithDummyResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'space_uuid' => $this->resource->space_uuid,
            'dummy_users' => $this->resource->dummyRelations->pluck('dummyUsers')->pluck('id'),
        ];
    }
}
