<?php

namespace Modules\Messenger\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class ChannelWithUserResource extends Resource {
    /**
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'channel_uuid'   => $this->uuid,
            'channel_name' => $this->channel_name,
            'channel_type' => $this->channel_type,
            'is_private'   => $this->is_private,
            'users' => ChannelUserResource::collection($this->users),
        ];
    }
}
