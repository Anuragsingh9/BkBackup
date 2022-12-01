<?php

namespace Modules\KctAdmin\Entities;

use App\Models\User;
use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @property string name
 * @property string short_name
 * @property string description
 * @property array settings
 * @property string group_key
 * @property int is_default
 *
 * @method static create($param)
 * @method static where(string $column, mixed $operator, mixed $value = null)
 */
class Group extends TenantModel {
    use SoftDeletes;

    protected $table = 'groups';
    protected $fillable = ['name', 'short_name', 'description', 'settings','group_key', 'is_default'];

    protected $casts = [
        'settings' => 'array',
    ];

    public function groupUser(): HasMany {
        $userExist = function ($q) {
            $q->where('deleted_at', null);
        };
        return $this->hasMany(GroupUser::class, 'group_id', 'id')
            ->with('user')->whereHas('user', $userExist);
    }

    public function groupType(): HasOneThrough {
        return $this->hasOneThrough(
            GroupType::class,
            GroupTypeRelation::class,
            'group_id',
            'id',
            'id',
            'type_id'
        );
    }

    public function security(): HasMany {
        return $this->hasMany(Security::class, 'id', 'security_atr_id');
    }

    public function allSettings(): HasMany {
        return $this->hasMany(GroupSetting::class, 'group_id', 'id');
    }

    public function organiser(): HasMany {
        return $this->hasMany(GroupUser::class, 'group_id', 'id')->where('role', GroupUser::$role_Organiser);
    }

    public function setting() {
        return $this->hasOne(GroupSetting::class, 'group_id', 'id');
    }

    public function mainSetting(): HasOne {
        return $this->hasOne(GroupSetting::class, 'group_id', 'id')->where('setting_key', 'main_setting' );

    }

    public function pilots(): HasManyThrough {
        return $this->hasManyThrough(
            User::class,
            GroupUser::class,
            'group_id',
            'id',
            'id',
            'user_id'
        )->whereRole(GroupUser::$role_Organiser);
    }

    public function owner(): HasMany{
        return $this->hasMany(GroupUser::class,'group_id','id')->whereRole(GroupUser::$role_owner);
    }

    public function events(): HasManyThrough {
        return $this->hasManyThrough(
            Event::class,
            GroupEvent::class,
            'group_id',
            'event_uuid',
            'id',
            'event_uuid'
        );
    }

    public function isFavGroup(): HasMany {
        return $this->hasMany(UserFavGroup::class, 'group_id', 'id');
    }

    public function settings(): HasMany {
        return $this->hasMany(
            GroupSetting::class,
            'group_id',
            'id',
        );
    }

    public function labelSetting(): HasMany {
        return $this->hasMany(
            LabelLocale::class,
            'group_id',
            'id',
        );
    }

    public function coPilots() {
        return $this->hasManyThrough(
            User::class,
            GroupUser::class,
            'group_id',
            'id',
            'id',
            'user_id'
        )->whereRole(GroupUser::$role_co_pilot);
    }

    public function admins(): HasMany {
        return $this->hasMany(
            GroupUser::class,
            'group_id',
            'id'
        )->whereIn('role', [
            GroupUser::$role_Organiser,
            GroupUser::$role_owner,
            GroupUser::$role_co_pilot
        ]);
    }

}
