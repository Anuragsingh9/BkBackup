<?php

namespace Modules\Cocktail\Transformers\UserSide;

use Illuminate\Http\Resources\Json\Resource;

class EventSpaceWithConversationResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        // front team asked to send the single users to appear as in conversation
        $singleUsers = $this->singleUsers->map(function ($row) {
            $row->users = collect([$row]);
            $row->uuid = null;
            return $row;
        });
        $conversations = $this->conversations->merge($singleUsers);
        
        return [
            'space'         => new EventSpaceResource($this->resource),
            'conversations' => ConversationResource::collection($conversations),
        ];
    }
}
