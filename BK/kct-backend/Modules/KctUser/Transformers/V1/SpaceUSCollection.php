<?php

namespace Modules\KctUser\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctAdmin\Transformers\EntityResource;
use Modules\KctUser\Transformers\V1\ChimeUSResource;
use Modules\UserManagement\Entities\EntityUser;

/**
 * @OA\Schema(
 *  title="APIResource: EventResource",
 *  description="Physical Event Resource",
 *  @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",),
 *  @OA\Property(property="data",type="object",description="",
 *      @OA\Property(property="current_space_host",type="object",description="Current Space Host Data",ref="#/components/schemas/HostResource"),
 *      @OA\Property(property="current_joined_conversation",type="object",description="Current Joined Conversation if any",ref="#/components/schemas/ChimeUSResource"),
 *      @OA\Property(property="current_joined_space",type="object",description="Current Joined Space",ref="#/components/schemas/SpaceUSResource"),
 *      @OA\Property(property="current_space_conversations",type="array",description="Current Space Conversations List",@OA\Items(ref="#/components/schemas/ChimeUSResource")),
 *      @OA\Property(property="spaces",type="array",description="Current Event Spaces",@OA\Items(ref="#/components/schemas/SpaceUSResource")),
 *  )
 * )
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class contain the resource of space data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class SpaceUSCollection
 *
 * @package Modules\KctUser\Transformers\V1
 */
class SpaceUSCollection extends JsonResource {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Transform the resource into an array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        return [
            'status' => true,
            'data'   => [
                'current_space_host'          => $this->getCurrentHost(), // current hosts
                'current_joined_conversation' => $this->getCurrentConversation(), //
                'current_joined_space'        => $this->getCurrentSpace(),
                'current_space_conversations' => $this->getConversations(), // minimal information of conversation like id and users
                'spaces'                      => SpaceUSResource::collection($this->resource->spaces), // minimal info of space
            ],
//            'meta'   => KctCoreService::getInstance()->metaForEventVersion($this->resource),
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the current host and return the host resource.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return AnonymousResourceCollection|void
     */
    private function getCurrentHost() {
        $currentSpace = $this->resource->currentSpace->space_uuid;
        foreach ($this->resource->spaces as $space) {
            if ($space->space_uuid == $currentSpace) {
                $hosts = $space['hosts'];
                return HostUSResource::collection($hosts);
            }
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the current conversation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return \Modules\KctUser\Transformers\V1\ChimeUSResource|null
     */
    private function getCurrentConversation() {
        return isset($this->resource->currentSpace->currentConversation) ? new ChimeUSResource($this->resource->currentSpace->currentConversation) : null;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the current space
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array|null
     */
    private function getCurrentSpace() {
        return $this->resource->currentSpace ? ['space_uuid' => $this->resource->currentSpace->space_uuid] : null;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the conversations
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array|AnonymousResourceCollection
     */
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
            $conversations = ConversationUSResource::collection($conversations);
        }
        return $conversations;
    }
}
