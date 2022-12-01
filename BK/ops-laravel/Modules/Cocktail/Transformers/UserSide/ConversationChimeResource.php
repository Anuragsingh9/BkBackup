<?php

namespace Modules\Cocktail\Transformers\UserSide;

use Illuminate\Http\Resources\Json\Resource;
use Modules\Cocktail\Transformers\UserBadgeResource;

class ConversationChimeResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'conversation_uuid'  => $this->uuid,
            'conversation_users' => UserBadgeResource::collection($this->users),
            'meeting'            => [
                'meeting_response'  => ['Meeting' => $this->aws_chime_meta['Meeting']],
                'attendee_response' => ['Attendee' => $this->currentUser->chime_attendee['Attendee']],
            ],
        ];
    }
}
