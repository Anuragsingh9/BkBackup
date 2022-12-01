<?php

namespace Modules\Messenger\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class MessageResource extends Resource {
    /**
     * This resource is for only message to show not with replies
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'message_id'   => $this->id,
            'message_text' => $this->message_text,
            'sender_id'    => $this->sender_id,
            'sent_on'      => $this->created_at->toDateTimeString(),
            'likes'        => $this->likes_count,
            'is_stared'    => $this->is_stared_count ? 1 : 0,
            'reply_count'  => $this->replies_count,
            'attachments'  => MessageAttachmentResource::collection($this->attachments),
        ];
    }
}
