<?php

namespace Modules\Cocktail\Transformers\AdminSide;

use Illuminate\Http\Resources\Json\Resource;

class EventBlueJeansResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        if (isset($this->bluejeans_settings['event_uses_bluejeans_event'])
            && $this->bluejeans_settings['event_uses_bluejeans_event'] == 1) {
            return [
                'event_uuid'                 => $this->event_uuid,
                'event_uses_bluejeans_event' => $this->bluejeans_settings['event_uses_bluejeans_event'],
                'event_chat'                 => $this->bluejeans_settings['event_chat'],
                'attendee_search'            => $this->bluejeans_settings['attendee_search'],
                'q_a'                        => $this->bluejeans_settings['q_a'],
                'allow_anonymous_questions'  => $this->bluejeans_settings['allow_anonymous_questions'],
                'auto_approve_questions'     => $this->bluejeans_settings['auto_approve_questions'],
                'auto_recording'             => $this->bluejeans_settings['auto_recording'],
                'phone_dial_in'              => $this->bluejeans_settings['phone_dial_in'],
                'raise_hand'                 => $this->bluejeans_settings['raise_hand'],
                'display_attendee_count'     => $this->bluejeans_settings['display_attendee_count'],
                'allow_embedded_replay'      => $this->bluejeans_settings['allow_embedded_replay']
            ];
        }
        return [
            'event_uuid'                 => $this->event_uuid,
            'event_uses_bluejeans_event' => 0,
        ];
        
    }
}
