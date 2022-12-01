<?php

namespace Modules\UserManagement\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;

class UserMobile extends TenantModel {

    public static $type_mobile = 1;
    public static $type_landLine = 2;

    protected $fillable = [
        'user_id',
        'country_code',
        'number',
        'is_primary',
        'type', // 1 mobile, 2 landline
    ];

}
