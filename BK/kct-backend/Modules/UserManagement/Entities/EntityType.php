<?php

namespace Modules\UserManagement\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;

/**
 * @property int id
 * @property string name
 * @property int level
 * @property int parent
 */
class EntityType extends TenantModel {

    protected $fillable = [
        'name',
        'level',
        'parent',
    ];

}
