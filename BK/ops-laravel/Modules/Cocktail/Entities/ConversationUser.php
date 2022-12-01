<?php

namespace Modules\Cocktail\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ConversationUser extends TenancyModel {
    protected $table = 'event_conversation_user';
    protected $fillable = ['conversation_uuid', 'user_id', 'chime_attendee'];
    protected $casts = ['chime_attendee' => 'array'];
    
    protected $hidden = ['created_at', 'update_at'];
    
    protected static function boot() {
        parent::boot();
        
        static::addGlobalScope('user_leave', function (Builder $builder) {
            $builder->whereNull('leave_at');
        });
    }
}
