<?php

namespace Modules\Messenger\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class MessageReplyResource extends Resource {
    /**
     * This is to represent the single reply of message only
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'message_reply_id' => $this->id,
            'reply_text'       => $this->reply_text,
            'replied_by'       => $this->replied_by,
            'send_on'          => $this->created_at->toDateTimeString(),
            'attachments'      => MessageAttachmentResource::collection($this->attachments),
        ];
    }
}
