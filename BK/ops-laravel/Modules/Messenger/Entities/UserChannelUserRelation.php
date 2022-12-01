<?php

namespace Modules\Messenger\Entities;

use App\User;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UserChannelUserRelation
 * @package Modules\Messenger\Entities
 * Used to store the User Personal Chat
 * This will hold channel id and two users id to store the relation
 * no user is treating as from or to so only single record is created, instead in query we put two where clause with or
 */
class UserChannelUserRelation extends TenancyModel {
    use SoftDeletes;
    protected $table = 'im_user_channel_user';
    protected $fillable = [
        'user1_id',
        'user2_id',
        'channel_uuid',
    ];
    
    /**
     * @return HasOne
     */
    public function user1() {
        return $this->hasOne(User::class, 'id', 'user1_id');
    }
    
    /**
     * @return HasOne
     */
    public function user2() {
        return $this->hasOne(User::class, 'id', 'user2_id');
    }
    
    /**
     * @return HasOne
     */
    public function channel() {
        return $this->hasOne(Channel::class, 'uuid', 'channel_uuid');
    }
    
}
