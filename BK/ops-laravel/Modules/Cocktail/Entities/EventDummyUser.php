<?php

namespace Modules\Cocktail\Entities;

use App\DummyUsers;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class EventDummyUser extends TenancyModel {
    protected $table = 'event_dummy_users';
    protected $fillable = ['event_uuid', 'dummy_user_id', 'current_conv_uuid'];
    
    public function dummyUsers() {
        return $this->belongsTo(DummyUsers::class, 'dummy_user_id', 'id');
    }
    
    public function conversation() {
        return $this->belongsTo(Conversation::class, 'current_conv_uuid', 'uuid');
    }
}
