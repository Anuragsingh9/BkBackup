<?php

namespace Modules\Cocktail\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class EventSpaceUser extends TenancyModel {
    public static $ROLE_HOST = 1;
    public static $ROLE_MEMBER = 2;
    
    protected $table = 'event_space_users';
    protected $fillable = ['space_uuid', 'user_id', 'role', 'current_conversation_uuid'];
    
    public function space() {
        return $this->hasOne(EventSpace::class, 'space_uuid', 'space_uuid');
    }
    
    public function event() {
        return $this->space()->event;
    }
    
    public function conversation() {
        return $this->hasOne(Conversation::class, 'uuid', 'current_conversation_uuid');
    }
}
