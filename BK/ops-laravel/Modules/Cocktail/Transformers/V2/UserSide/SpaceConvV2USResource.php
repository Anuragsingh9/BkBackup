<?php

namespace Modules\Cocktail\Transformers\V2\UserSide;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Modules\Cocktail\Transformers\UserSide\ConversationResource;
use Modules\Cocktail\Transformers\UserSide\EventSpaceResource;

class SpaceConvV2USResource extends Resource {
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
            'space'         => new EventSpaceResource($this->resource),
            'conversations' => ConversationV2USResource::collection($conversations),
        ];
    }
}
