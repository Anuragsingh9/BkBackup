<?php

namespace Modules\Events\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class EventResourceForMember extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        $type = [
            'int'     => 'Internal',
            'ext'     => 'External',
            'virtual' => 'Virtual',
        ];
        return [
            "type"        => isset($type[$this->type]) ? $type[$this->type] : null,
            "title"       => $this->title,
            "date"        => $this->date,
            "start_time"  => $this->start_time,
            "end_time"    => $this->end_time,
            "id"          => $this->id,
            'workshop_id' => $this->workshop_id,
        ];
    }
}
