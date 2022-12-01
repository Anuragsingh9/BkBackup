<?php

namespace Modules\Messenger\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class MessageWithReplyResource extends Resource {
    /**
     * This is to show the complete details of message like reply likes count etc.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'message_id' => $this->id,
            'message_text' => $this->message_text,
            'sender' => new ChannelUserResource($this->sender),
            'channel_uuid' => $this->channel_uuid,
            'likes_count' => $this->likeCount, // to show how many liked this message
            'is_started' => $this->is_stared, // if the current user has been stared this message
            'attachments' => $this->attachments ? MessageMediaResource::collection($this->attachments) : [],
            'replies_count' => $this->repliesCount,
            'replies' => $this->replies ? MessageReplyResource::collection($this->replies) : $this->replies,
        ];
    }
}
