<?php

namespace Modules\UserManagement\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;

class UserInfo extends TenantModel {

    protected $fillable = [
        'user_id',
        'fields',
        'city',
        'country',
        'address',
        'postal',
    ];

    protected $casts = [
        'fields' => 'array',
    ];
}


