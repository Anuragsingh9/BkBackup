<?php

namespace Modules\KctUser\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="API Resource: ConversationUSResource",
 *     description="Conversation V1 User Side Resource",
 *     @OA\Property(property="conversation_uuid",type="string",description="Unique conversation uuid",
 *     example="adfsd3hj34j5hjkh34h3"
 *     ),
 *     @OA\Property(property="conversation_type",type="string",description="single_user or active",example="single_user"),
 *     @OA\Property(property="is_conversation_private",type="integer",description="private= 1 normal=0",example="1"),
 *     @OA\Property(property="conversation_users",type="array",description="Users resource",
 *          @OA\Items(ref="#/components/schemas/BadgeUSResource")
 *     ),
 * ),
 *
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class resource contain the user conversation data
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class ConversationUSResource
 *
 * @package Modules\Cocktail\Transformers\V1\UserSide
 */
class ConversationUSResource extends JsonResource {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Transform the resource into an array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request
     * @return array
     */
    public function toArray($request) {
        return [
            'conversation_uuid'       => $this->uuid,
            'conversation_type'       => $this->uuid ? 'active' : 'single_user',
            'is_conversation_private' => $this->resource->is_private ? 1 : 0,
            'conversation_users'      => BadgeUSResource::collection($this->users),
        ];
    }
}
