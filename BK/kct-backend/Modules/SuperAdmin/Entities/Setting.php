<?php

namespace Modules\SuperAdmin\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string setting_key
 * @property array setting_value
 */
class Setting extends Model {

    protected $connection = 'mysql';

    protected $fillable = [
        'setting_key', 'setting_value', 'hostname_id',
    ];

    protected $casts = [
        'setting_value' => 'array',
    ];

}
