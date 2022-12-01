<?php

namespace Modules\Messenger\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class ChannelMessageResource extends Resource {
    
    public function toArray($request) {
        return [
            'message_id'   => $this->id,
            'message_text' => $this->message_text,
            'sender_id'    => $this->sender_id,
            'sent_on'      => $this->created_at->toDateTimeString(),
            'likes'        => $this->likes_count,
            'is_stared'    => $this->is_stared_count ? 1 : 0,
            'reply_count'  => $this->replies_count,
            'attachments'  => ($this->attachments->count() ? MessageAttachmentResource::collection($this->attachments) : []),
            'last_reply_sent_on' => (isset($this->replies) && $this->replies->count() ? $this->replies->first()->created_at->toDateTimeString(): null),
        ];
    }
}
