<?php

namespace Modules\KctAdmin\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;

/**
 * @method static GroupEvent|null create(array $array)
 */
class GroupEvent extends TenantModel {
    protected $fillable = [
        'group_id',
        'event_uuid',55
    ];
}
