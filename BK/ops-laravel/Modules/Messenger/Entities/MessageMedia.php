<?php

namespace Modules\Messenger\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageMedia extends TenancyModel {
    use SoftDeletes;
    protected $table = 'im_message_medias';
    protected $fillable = [
        'attachmentable_id',
        'media_url',
        'source',
        'title',
    ];
    
    public function attachmentable() {
        return $this->morphTo();
    }
    
    public function message() {
        return $this->hasOne(Message::class, 'id', 'attachmentable_id');
    }
    
    public function reply() {
        return $this->hasOne(MessageReply::class, 'id', 'attachmentable_id');
    }
}
