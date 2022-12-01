<?php

namespace Modules\KctAdmin\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;

/**
 * @property string setting_key
 * @property array setting_value
 * @method static GroupSetting create(array $array)
 * @method static where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static GroupSetting updateOrCreate(array $array1, array $array2)
 *
 * Class GroupSetting
 * @package Modules\KctAdmin\Entities
 */
class GroupSetting extends TenantModel {

    protected $fillable = ['setting_key', 'setting_value','group_id','follow_organisation'];

    protected $casts = [
        'setting_value' => 'array',
    ];
}
