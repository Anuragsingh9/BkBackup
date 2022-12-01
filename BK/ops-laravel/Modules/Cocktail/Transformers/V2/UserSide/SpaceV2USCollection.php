<?php

namespace Modules\Cocktail\Transformers\V2\UserSide;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Modules\Cocktail\Services\V2Services\KctCoreService;

class SpaceV2USCollection extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request) {
        return [
            'status' => true,
            'data'   => [
                'current_joined_conversation' => $this->getCurrentConversation(), //
                'current_joined_space'        => $this->getCurrentSpace(),
                'current_space_conversations' => $this->getConversations(), // minimal information of conversation like id and users
                'spaces'                      => SpaceV2USResource::collection($this->resource->spaces), // minimal info of space
            ],
            'meta'   => KctCoreService::getInstance()->metaForEventVersion($this->resource),
        ];
    }
    
    private function getCurrentConversation() {
        return isset($this->resource->currentSpace->currentConversation) ? new ChimeV2USResource($this->resource->currentSpace->currentConversation) : null;
    }
    
    private function getCurrentSpace() {
        return $this->resource->currentSpace ? ['space_uuid' => $this->resource->currentSpace->space_uuid] : null;
    }
    
    private function getConversations() {
        $conversations = [];
        // adding the single users to conversation key,
        // so single users will be shown as in single user only conversation with null conversation id
        if ($this->currentSpace) {
            $singleUsers = $this->currentSpace->singleUsers->map(function ($row) {
                $row->users = collect([$row]);
                $row->uuid = null;
                return $row;
            });
            // merging conversation after single users
            // so single users will be come first the conversation will come
            // this will make conversations by users count asc order
            $conversations = $singleUsers->merge($this->currentSpace->conversations);
            $conversations = ConversationV2USResource::collection($conversations);
        }
        return $conversations;
    }
}
