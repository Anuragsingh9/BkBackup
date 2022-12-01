<?php

namespace Modules\Messenger\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Modules\Messenger\Entities\Message;
use Modules\Messenger\Entities\MessageReply;

class MessageAttachmentResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'attachment_id' => $this->id,
            'extension'     => pathinfo($this->media_url, PATHINFO_EXTENSION),
            'url'           => $this->media_url,
            'file_name'     => $this->title,
            'sender_id'     => ($this->attachmentable_type == Message::class) ? $this->message->sender_id : ($this->attachmentable_type == MessageReply::class ? $this->reply->replied_by : NULL),
            'sent_on'       => ($this->attachmentable_type == Message::class) ? $this->message->created_at->toDateTimeString() : ($this->attachmentable_type == MessageReply::class ? $this->reply->created_at->toDateTimeString() : NULL),
        ];
    }
}
