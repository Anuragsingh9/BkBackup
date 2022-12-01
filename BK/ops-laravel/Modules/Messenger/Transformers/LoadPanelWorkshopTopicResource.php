<?php

namespace Modules\Messenger\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class LoadPanelWorkshopTopicResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
//        return parent::toArray($request);
        return [
            'topic_id' => $this->id,
            'topic_name' => $this->topic_name,
            'channel_uuid' => $this->channel_uuid,
            'unread_count' => (($this->channel && $this->channel->unreadMessages) ? $this->channel->unreadMessages->count() : 0),
//            'unread_messages' => $this->channel ? $this->channel->unreadMessages : null,
        ];
    }
}
