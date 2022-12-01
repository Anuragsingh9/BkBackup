<?php

namespace Modules\KctUser\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventUserInvites extends TenancyModel {
    use SoftDeletes;

    protected $table = 'kct_user_invites';
    protected $fillable = [
        'invited_by_user_id',
        'event_uuid',
        'first_name',
        'last_name',
        'email'
    ];
}
