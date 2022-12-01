<?php

namespace Modules\Messenger\Entities;

use App\User;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageReaction extends TenancyModel {
    use SoftDeletes;
    protected $table = 'im_message_reactions';
    protected $fillable = [
        'message_id',
        'reaction_type',
        'reacted_by',
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
    public function reactedBy() {
        return $this->hasOne(User::class, 'id', 'reacted_by');
    }
}
