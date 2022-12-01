<?php

namespace Modules\Cocktail\Transformers\AdminSide;

use Illuminate\Http\Resources\Json\Resource;

class EventRegistrationDetailsResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        if (isset($this->event_fields['registration_details'])) {
            $display = $this->event_fields['registration_details']['display'] ? 1 : 0;
        } else {
            $display = 0;
        }
        return [
            'display' => $display,
            'title'   => isset($this->event_fields['registration_details']) ? $this->event_fields['registration_details']['title'] : '',
            'points'  => isset($this->event_fields['registration_details']) ? $this->event_fields['registration_details']['points'] : '',
        ];
    }
}
