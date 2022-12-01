<?php

namespace Modules\Events\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Eventable extends TenancyModel {
    use SoftDeletes;

    protected $table = 'eventables';
    protected $fillable = [
        'event_id',
        'eventable_id',
        'eventable_type',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
