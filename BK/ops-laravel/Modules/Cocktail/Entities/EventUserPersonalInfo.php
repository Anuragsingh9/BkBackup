<?php

namespace Modules\Cocktail\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Model;

class EventUserPersonalInfo extends TenancyModel {
    protected $table = 'event_user_personal_info';
    
    protected $fillable = [
        'user_id',
        'field_1',
        'field_2',
        'field_3',
    ];
}
