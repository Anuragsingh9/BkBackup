<?php

namespace Modules\KctUser\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *  title="APIResource: ChimeUSResource",
 *  description="Physical Event Resource",
 *  @OA\Property(property="conversation_uuid",type="UUID",
 *     description="Conversation Uuid",example="01493146-d018-11ea-9d2a-b82a72a009b4"),
 *  @OA\Property(property="conversation_users",type="array",description="Conversation Users",
 *     @OA\Items(ref="#/components/schemas/BadgeUSResource")
 *  ),
 *  @OA\Property(property="is_conversation_private",type="integer",
 *     description="To indicate if conversation is private", example="1",),
 *  @OA\Property(property="meeting",type="object",description="",
 *      @OA\Property(property="meeting_response",type="object",description="Video Conversation Meeting Response"),
 *      @OA\Property(property="attendee_response",type="object",description="Video Conversation Attendee Response")
 *  )
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will be managing the chime user resource
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class ChimeUSResource
 *
 * @package Modules\KctUser\Transformers\V1
 */
class ChimeUSResource extends JsonResource {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Transform the resource into an array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        return [
            'conversation_uuid'       => $this->uuid,
            'conversation_users'      => BadgeUSResource::collection($this->users),
            'is_conversation_private' => $this->resource->is_private ? 1 : 0,
            'meeting'                 => [
                'meeting_response'  => ['Meeting' => $this->aws_chime_meta['Meeting']],
                'attendee_response' => ['Attendee' => $this->currentUser->chime_attendee['Attendee']],
            ],
        ];
    }
}
