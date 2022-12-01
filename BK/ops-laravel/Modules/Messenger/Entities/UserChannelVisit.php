<?php

namespace Modules\Messenger\Entities;

use App\User;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UserChannelVisit
 * @package Modules\Messenger\Entities
 * Used to store when the user has visited that channel so that we can found messages for that user after visit will be as unread message
 */
class UserChannelVisit extends TenancyModel {
    use SoftDeletes;
    protected $table = 'im_user_channel_visits';
    protected $fillable = [
        'user_id',
        'channel_uuid',
        'last_visited_at',
        'is_hidden',
    ];
    
    /**
     * @return HasOne
     */
    public function channel() {
        return $this->hasOne(Channel::class, 'uuid', 'channel_uuid');
    }
    
    /**
     * @return HasOne
     */
    public function user() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    
    
}
