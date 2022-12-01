<?php

namespace Modules\Events\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class EventOccupiedDateResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'id'         => $this->id,
            'date'       => $this->date,
            'start_time' => $this->start_time,
            'end_time'   => $this->end_time,
        ];
    }
}
