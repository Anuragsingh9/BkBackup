<?php

namespace Modules\Messenger\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class LoadPanelChannelResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'channel_uuid' => $this->uuid,
            'channel_name' => $this->channel_name,
            'is_private'   => $this->is_private,
            'owner_id'     => $this->owner_id,
            'unread_count' => $this->unreadMessages->count(),
            'members_count'  => $this->users_count,
        ];
    }
}
