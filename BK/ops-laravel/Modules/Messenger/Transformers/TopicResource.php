<?php

namespace Modules\Messenger\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class TopicResource extends Resource
{
    /**
     * This resource is for only message to show not with replies
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'topic_id' => $this->id,
            'topic_name' => $this->topic_name,
            'channel_uuid' => $this->channel_uuid,
        ];
    }
}
