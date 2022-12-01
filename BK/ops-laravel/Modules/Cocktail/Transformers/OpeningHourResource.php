<?php

namespace Modules\Cocktail\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class OpeningHourResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'before' => isset($this['before']) ? (int)$this['before'] : null,
            'during' => isset($this['before']) ? (int)$this['during'] : null,
            'after' => isset($this['before']) ? (int)$this['after'] : null,
        ];
    }
}
