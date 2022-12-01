<?php

namespace Modules\Cocktail\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Model;

class UserVisibility extends TenancyModel {
    protected $table = 'event_kct_user_visibility';
    protected $fillable = ['user_id', 'fields'];
    protected $casts = ['fields' => 'array'];
    
}
