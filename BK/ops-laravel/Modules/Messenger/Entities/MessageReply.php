<?php

namespace Modules\Messenger\Entities;

use App\User;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageReply extends TenancyModel {
    use SoftDeletes;
    protected $table = 'im_message_replies';
    protected $fillable = [
        'message_id',
        'reply_text',
        'replied_by',
    ];
    
    /**
     * @return HasOne
     */
    public function message() {
        return $this->hasOne(Message::class, 'id', 'message_id');
    }
    
    /**
     * @return HasOne
     */
    public function repliedBy() {
        return $this->hasOne(User::class, 'id', 'replied_by');
    }
    
    public function attachments() {
        return $this->morphMany(MessageMedia::class, 'attachmentable');
    }
}

