<?php

namespace Modules\Messenger\Entities;

use App\User;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChannelUser extends TenancyModel {
    use SoftDeletes;
    protected $table = 'im_channel_users';
    protected $fillable = [
        'user_id',
        'channel_uuid',
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
