<?php

namespace Modules\KctUser\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class UserBan extends TenancyModel {
    protected $table = 'kct_user_bans';
    protected $fillable = [
        'user_id',
        'severity',
        'ban_reason',
        'ban_type',
        'banable_type', // event,
        'banable_id',
        'banned_by',
    ];
}
