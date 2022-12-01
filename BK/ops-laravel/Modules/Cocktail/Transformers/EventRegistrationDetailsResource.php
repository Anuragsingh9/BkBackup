<?php

namespace Modules\Cocktail\Transformers;

use Illuminate\Http\Resources\Json\Resource;


/**
 * @OA\Schema(
 *  title="APIResource: EventRegistrationDetailsResource",
 *  description="Api resource contains the detail for event registration purpose.",
 *  @OA\Property(
 *      property="display",
 *      type="integer",
 *      description="Indicates to show the registration details for the event or not",
 *      example="0"
 *  ),
 *  @OA\Property(
 *      property="title",
 *      type="string",
 *      description="Title to show when user regiter a event",
 *      example="You will learn something in this event"
 *  ),
 *  @OA\Property(
 *      property="points",
 *      type="string",
 *      description="The key points to show for event registration title",
 *      example="Point 1,Point 2"
 *  ),
 * )
 *
 * Class EventRegistrationDetailsResource
 * @package Modules\Cocktail\Transformers
 */
class EventRegistrationDetailsResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'display' => isset($this->event_fields['registration_details']) ? $this->event_fields['registration_details']['display'] : '',
            'title'   => isset($this->event_fields['registration_details']) ? $this->event_fields['registration_details']['title'] : '',
            'points'  => isset($this->event_fields['registration_details']) ? $this->event_fields['registration_details']['points'] : '',
        ];
    }
}
