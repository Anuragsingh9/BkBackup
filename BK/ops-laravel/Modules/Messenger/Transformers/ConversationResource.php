<?php

namespace Modules\Messenger\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class ConversationResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'channel_uuid'   => $this->uuid,
            'channel_name' => $this->channel_name,
            'channel_type' => $this->channel_type,
            'is_private'   => $this->is_private,
            'messages' => $this->messages ? ChannelMessageResource::collection($this->messages) : null,
        ];
    }
}
