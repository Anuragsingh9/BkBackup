<?php

namespace Modules\Cocktail\Transformers\UserSide;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Modules\Events\Service\ValidationService;

class EventSpaceCollectionResource extends Resource {
    
    /**
     * Transform the resource collection into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request) {
        return [
            'status' => true,
            'data'   => [
                'active_conversation' => $this->getCurrentConversation(), //
                'active_space'        => $this->getCurrentSpace(),
                'conversations'       => $this->getConversations(), // minimal information of conversation like id and users
                'spaces'              => EventSpaceResource::collection($this->spaces), // minimal info of space
            ],
        ];
    }
    
    private function getCurrentConversation() {
        $result = null;
        if ($this->currentSpace && $this->currentSpace->currentConversation && ValidationService::getInstance()->isSpaceOpen($this->currentSpace)) {
            $result =  $this->currentSpace->currentConversation;
        }
        return $result ? new ConversationChimeResource($result) : null;
    }
    
    private function getCurrentSpace() {
        return $this->currentSpace ? new EventSpaceResource($this->currentSpace) : null;
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
            $conversations = $this->currentSpace->conversations->merge($singleUsers);
            $conversations = ConversationResource::collection($conversations);
        }
        return $conversations;
    }
    
    
}
