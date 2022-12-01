<?php

namespace Modules\UserManagement\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int entity_type_id
 * @property string long_name
 */
class Entity extends TenantModel {
    public static int $type_companyType = 1;
    public static int $type_unionType = 2;

    protected $fillable = [
        'entity_type_id',
        'long_name',
    ];

    public function entityUsersRelation(): HasMany {
        return $this->hasMany(EntityUser::class, 'entity_id', 'id');
    }

}
