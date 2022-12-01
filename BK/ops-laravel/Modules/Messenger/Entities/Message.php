<?php

namespace Modules\Messenger\Entities;

use App\User;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Modules\Messenger\Http\Requests\MessageReactionRequest;

class Message extends TenancyModel {
    use SoftDeletes;
    protected $table = 'im_messages';
    
    protected $fillable = [
        'message_text',
        'channel_uuid',
        'sender_id',
    ];
    
    /**
     * boot method to also delete the message related stuff
     * it will not work with query builder as first needed to fetch the message to make deleting method call
     */
    protected static function boot() {
        parent::boot();
        static::deleting(function ($message) {
            $message->replies()->delete();
            $message->reactions()->delete();
            $message->attachments()->delete();
        });
    }
    
    /**
     * @return HasOne
     */
    public function channel() {
        return $this->hasOne(Channel::class, 'uuid', 'channel_uuid');
    }
    
    /**
     * @return HasOne
     */
    public function sender() {
        return $this->hasOne(User::class, 'id', 'sender_id');
    }
    
    /**
     * @return HasMany
     */
    public function likes() {
        return $this->hasMany(MessageReaction::class, 'message_id', 'id')->where('reaction_type', 2);
    }
    
    /**
     * @return HasMany
     */
    public function replies() {
        return $this->hasMany(MessageReply::class, 'message_id', 'id');
    }
    
    /**
     * @return HasMany
     */
    public function reactions() {
        return $this->hasMany(MessageReaction::class, 'message_id', 'id');
    }
    
    /**
     * @return HasMany
     */
//    public function attachments() {
//        return $this->hasMany(MessageMedia::class, 'message_id', 'id');
//    }
    
    /**
     * @return HasOne
     * Check the current message is stared by the current user or not
     */
    public function isStared() {
        return $this->hasOne(MessageReaction::class, 'message_id', 'id')
            ->where('reaction_type', 1)
            ->where('reacted_by', Auth::user()->id);
    }
    
    public function attachments() {
        return $this->morphMany(MessageMedia::class, 'attachmentable');
    }
    
    
}
