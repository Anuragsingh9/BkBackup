<?php

namespace Modules\Cocktail\Transformers\UserSide;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class UserEventResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        $organiser = $this->users->count() ? $this->users->first() : null;
        $eventUser = $this->eventUsers->count() ? $this->eventUsers->first() : false;
        return [
            'event_uuid'       => $this->event_uuid,
            'event_title'      => $this->title,
            'event_date'       => Carbon::createFromFormat('Y-m-d', $this->date)->format("Y/m/d"),
            'event_start_time' => Carbon::createFromFormat('H:i:s', $this->start_time)->format(config('kct_const.user_side_date_format')),
            'event_end_time'   => Carbon::createFromFormat('H:i:s', $this->end_time)->format(config('kct_const.user_side_date_format')),
            'is_participant'   => (boolean)$this->eventUsers->count(),
            'organiser_fname'  => $organiser ? $organiser->fname : null,
            'organiser_lname'  => $organiser ? $organiser->lname : null,
            'is_presenter'     => $eventUser ? $eventUser->pivot->is_presenter : 0,
            'is_moderator'     => $eventUser ? $eventUser->pivot->is_moderator : 0,
            'is_host'          => $this->isHostOfAnySpace->count() ? 1 : 0,
        ];
    }
}
