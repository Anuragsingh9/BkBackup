<?php

namespace Modules\Messenger\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class LoadPanelUserResource extends Resource
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
            'user_id' => $this->id,
            'channel_uuid' => $this->channel_uuid,
            'unread_count' => ($this->channel && $this->channel->unreadMessages ? $this->channel->unreadMessages->count() : 0),
        ];
    }
}
