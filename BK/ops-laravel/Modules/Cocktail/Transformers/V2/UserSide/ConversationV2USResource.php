<?php

namespace Modules\Cocktail\Transformers\V2\UserSide;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

class ConversationV2USResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request) {
        return [
            'conversation_uuid'  => $this->uuid,
            'conversation_type'  => $this->uuid ? 'active' : 'single_user',
            'conversation_users' => BadgeV2USResource::collection($this->users),
        ];
    }
}
