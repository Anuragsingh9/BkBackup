<?php

namespace Modules\Cocktail\Transformers\UserSide;

use Illuminate\Http\Resources\Json\Resource;
use Modules\Cocktail\Transformers\UserBadgeResource;

class ConversationResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'conversation_uuid'  => $this->uuid,
            'conversation_type'  => $this->uuid ? 'active' : 'single_user',
            'conversation_users' => UserBadgeResource::collection($this->users),
        ];
    }
}
