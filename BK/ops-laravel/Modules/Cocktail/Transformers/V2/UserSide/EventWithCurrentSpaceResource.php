<?php

namespace Modules\Cocktail\Transformers\V2\UserSide;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Modules\Cocktail\Transformers\EventRegistrationDetailsResource;

class EventWithCurrentSpaceResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request) {
        return [
            'event_title'          => $this->resource->title,
            'event_uuid'           => $this->resource->event_uuid,
            'date'                 => Carbon::createFromFormat('Y-m-d', $this->resource->date)->format("Y/m/d"),
            'start_time'           => Carbon::createFromFormat(config('cocktail.event_time_db_format'), $this->resource->start_time)->format(config('kct_const.user_side_date_format')),
            'end_time'             => Carbon::createFromFormat(config('cocktail.event_time_db_format'), $this->resource->end_time)->format(config('kct_const.user_side_date_format')),
            'registration_details' => ((isset($this->event_fields['registration_details']) &&
                $this->resource->event_fields['registration_details']['display'] == 1)
                ? new EventRegistrationDetailsResource($this) : ["display" => 0]),
            'spaces' => SpaceV2USResource::collection($this->resource->spaces),
            'current_space'        =>
                (isset($this->resource->currentSpace) && $this->resource->currentSpace)
                    ? new SpaceV2USResource($this->resource->currentSpace)
                    : null,
        ];
    }
}
