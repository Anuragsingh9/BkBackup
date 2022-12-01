<?php

namespace Modules\Cocktail\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

/**
 * @OA\Schema(
 *  title="APIResource: EventResourcePublic",
 *  description="Event Api resource contains some basic details for event which any one can see",
 *  @OA\Property(
 *      property="event_title",
 *      type="string",
 *      description="Title of Event",
 *      example="Event Title Name"
 *  ),
 *  @OA\Property(
 *      property="event_uuid",
 *      type="UUID",
 *      description="Unique UUID of Event",
 *      example="123e4567-e89b-12d3-a456-426614174000"
 *  ),
 *  @OA\Property(
 *      property="date",
 *      type="date",
 *      description="Date of event start YYYY/MM/DD",
 *      example="2020/12/31"
 *  ),
 *  @OA\Property(
 *      property="start_time",
 *      type="time",
 *      description="Start time of event in HH:II:SS",
 *      example="20:59:59"
 *  ),
 *  @OA\Property(
 *      property="end_time",
 *      type="time",
 *      description="End time of event in HH:II:SS",
 *      example="20:59:59"
 *  ),
 *  @OA\Property(
 *      property="registration_details",
 *      type="integer",
 *      description="The registration details to show user for what the event is organising.",
 *      ref="#/components/schemas/EventRegistrationDetailsResource",
 *  ),
 *  @OA\Property(
 *      property="space_moods",
 *      type="array",
 *      description="Different Spaces with space moods of that event",
 *      @OA\Items(
 *          ref="#/components/schemas/EventSpaceResourcePublic",
 *      ),
 *  ),
 * )
 *
 * Class EventResourcePublic
 * @package Modules\Cocktail\Transformers
 */
class EventResourcePublic extends Resource {
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
            'start_time'           => Carbon::createFromFormat(config('cocktail.event_time_db_format'),$this->start_time)->format( config('kct_const.user_side_date_format')),
            'end_time'             => Carbon::createFromFormat(config('cocktail.event_time_db_format'),$this->end_time)->format( config('kct_const.user_side_date_format')),
            'registration_details' => ((isset($this->event_fields['registration_details']) &&
                $this->event_fields['registration_details']['display'] == 1)
                ? new EventRegistrationDetailsResource($this) : ["display" => 0]),
            'space_moods'          => EventSpaceResourcePublic::collection($this->spaces),
        ];
    }
}
