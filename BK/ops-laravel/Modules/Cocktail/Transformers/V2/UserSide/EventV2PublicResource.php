<?php

namespace Modules\Cocktail\Transformers\V2\UserSide;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;
use Modules\Cocktail\Transformers\EventRegistrationDetailsResource;
use Modules\Cocktail\Transformers\EventSpaceResourcePublic;

class EventV2PublicResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'event_title'          => $this->title,
            'event_uuid'           => $this->event_uuid,
            'date'                 => Carbon::createFromFormat('Y-m-d', $this->date)->format("Y/m/d"),
            'start_time'           => Carbon::createFromFormat(config('cocktail.event_time_db_format'), $this->start_time)->format(config('kct_const.user_side_date_format')),
            'end_time'             => Carbon::createFromFormat(config('cocktail.event_time_db_format'), $this->end_time)->format(config('kct_const.user_side_date_format')),
            'registration_details' => ((isset($this->event_fields['registration_details']) &&
                $this->event_fields['registration_details']['display'] == 1)
                ? new EventRegistrationDetailsResource($this) : ["display" => 0]),
            'space_moods'          => EventSpaceResourcePublic::collection($this->spaces),
            'header_line_1' => $this->resource->header_line_1,
            'header_line_2' => $this->resource->header_line_2,
        ];
    }
}
