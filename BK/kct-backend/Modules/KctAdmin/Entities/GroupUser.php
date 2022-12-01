<?php

namespace Modules\KctAdmin\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\UserManagement\Entities\User;

/**
 * @method static create(string[] $array)
 */
class GroupUser extends TenantModel {

    public static int $role_User = 1;
    public static int $role_Organiser = 2;
    public static int $role_owner = 3;
    public static int $role_co_pilot = 4;

    protected $fillable = ['group_id', 'user_id', 'role','last_visit'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function group(): HasMany {
        return $this->hasMany(Group::class,'id','group_id');
    }
}
