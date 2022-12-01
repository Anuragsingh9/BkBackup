<?php

namespace Modules\Cocktail\Transformers\V2\UserSide;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

class ChimeV2USResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request) {
        return [
            'conversation_uuid'  => $this->uuid,
            'conversation_users' => BadgeV2USResource::collection($this->users),
            'meeting'            => [
                'meeting_response'  => ['Meeting' => $this->aws_chime_meta['Meeting']],
                'attendee_response' => ['Attendee' => $this->currentUser->chime_attendee['Attendee']],
            ],
        ];
    }
}
