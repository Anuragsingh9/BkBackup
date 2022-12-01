<?php

namespace Modules\Cocktail\Transformers\V2\UserSide;

use Illuminate\Http\Resources\Json\Resource;
use Modules\Cocktail\Services\V2Services\KctCoreService;

class EventV2USResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'event_uuid'        => $this->event_uuid,
            'event_title'       => $this->title,
            'event_header_text' => $this->header_text,
            'event_description' => $this->description,
            'event_date'        => $this->date,
            'event_start_time'  => $this->start_time,
            'event_end_time'    => $this->end_time,
            "header_line_one"   => $this->header_line_1,
            "header_line_two"   => $this->header_line_2,
            "manual_opening"    => $this->manual_opening,
            "is_dummy_event"    => isset($this->event_fields["is_dummy_event"]) ? checkValSet($this->event_fields["is_dummy_event"]) : 0,
            "event_image"       => $this->image,
            "conference_type"   => KctCoreService::getInstance()->findEventConferenceType($this->resource),
        ];
    }
}
