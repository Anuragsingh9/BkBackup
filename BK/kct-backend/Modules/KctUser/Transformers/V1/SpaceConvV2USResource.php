<?php

namespace Modules\KctUser\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\Resource;
use Modules\KctUser\Transformers\UserSide\ConversationResource;
use Modules\KctUser\Transformers\UserSide\EventSpaceResource;

/**
 * @OA\Schema(
 *  title="APIResource: SpaceConvV2USResource",
 *  description="Physical Event Resource",
 *  @OA\Property(property="space",type="object",description="Space data",ref="#/components/schemas/SpaceUSResource"),
 *  @OA\Property(property="conversations",type="array",description="Current Space Conversations List",@OA\Items(ref="#/components/schemas/ConversationResource")),
 * )
 *
 * -----------------------------------------------------------------------------------------------------------------
 * @descripiton This class will be used for returning the space data
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class SpaceConvV2USResource
 */
class SpaceConvV2USResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param Request
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
            'space'         => new SpaceUSResource($this->resource),
            'conversations' => ConversationUSResource::collection($conversations),
        ];
    }
}
