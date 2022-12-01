<?php

namespace Modules\KctUser\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *  title="APIResource: ConversationResource",
 *  description="",
 *  @OA\Property(property="conversation_uuid",type="UUID",description="Conversation Uuid",
 *     example="01493146-d018-11ea-9d2a-b82a72a009b4"),
 *  @OA\Property(property="conversation_users",type="array",description="Conversation Users",
 *     @OA\Items(ref="#/components/schemas/BadgeUSResource")
 *  ),
 *  @OA\Property(property="conversation_type",type="enum",
 *     description="To indicate if conversation is real or mapped for single user", enum={"active", "single_user"}
 *     ),
 *  )
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class contain the users conversation data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class ConversationResource
 *
 * @package Modules\KctUser\Transformers\V1
 */
class ConversationResource extends JsonResource {

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
            'conversation_uuid'  => $this->uuid,
            'conversation_type'  => $this->uuid ? 'active' : 'single_user',
            'conversation_users' => BadgeUSResource::collection($this->users),
        ];
    }
}
