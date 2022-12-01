<?php

namespace Modules\UserManagement\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EntityUser extends TenantModel {

    protected $fillable = [
        'user_id',
        'entity_id',
        'created_by',
        'position',
    ];

    public function entity(): HasOne {
        return $this->hasOne(Entity::class, 'id', 'entity_id');
    }

    public function entityUser(){
        return $this->belongsTo(\App\Models\User::class,'user_id','id');
    }

}
