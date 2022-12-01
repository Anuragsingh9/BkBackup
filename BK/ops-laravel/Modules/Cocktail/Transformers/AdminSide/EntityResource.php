<?php

namespace Modules\Cocktail\Transformers\AdminSide;

use Illuminate\Http\Resources\Json\Resource;

class EntityResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'entity_id'  => $this->id,
            'long_name'  => $this->long_name,
            'short_name' => $this->short_name,
        ];
    }
}
