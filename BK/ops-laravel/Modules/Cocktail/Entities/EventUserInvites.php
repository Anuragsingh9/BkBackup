<?php

namespace Modules\Cocktail\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Model;

class EventUserInvites extends TenancyModel {
    protected $table = 'event_user_invites';
    protected $fillable = [
        'invited_by_user_id',
        'event_uuid',
        'first_name',
        'last_name',
        'email'
    ];
}
