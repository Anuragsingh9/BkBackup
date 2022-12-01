<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Modules\KctAdmin\Entities\EventUser;
use Modules\KctAdmin\Entities\Group;
use Modules\KctAdmin\Entities\GroupUser;
use Modules\KctAdmin\Entities\OrganiserTag;
use Modules\KctAdmin\Entities\UserMetas;
use Modules\KctUser\Entities\OtpCode;
use Modules\KctUser\Entities\UserClmVisibility;
use Modules\KctUser\Entities\UserTag;
use Modules\UserManagement\Entities\Entity;
use Modules\UserManagement\Entities\EntityUser;
use Modules\UserManagement\Entities\UserInfo;
use Modules\UserManagement\Entities\UserMobile;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int id
 * @property string fname
 * @property string lname
 * @property string email
 * @property string password
 * @property string|null avatar
 * @property string|null identifier
 * @property string|null setting
 * @property string|null internal_id
 * @property string|null email_verified_at
 *
 * @method static User create(array $array)
 * @method static User find(int $id)
 *
 * Class User
 * @package App\Models
 */
class User extends Authenticatable {
    use HasFactory, Notifiable, UsesTenantConnection, HasApiTokens, HasRoles, SoftDeletes;

    public static $userRoles = ['executive', 'employee', 'manager', 'other'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fname',
        'lname',
        'email',
        'password',
        'avatar',
        'identifier',
        'login_count',
        'setting',
        'internal_id',
        'email_verified_at',
        'gender',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'setting'           => 'array',
    ];

    /*
     * RELATIONS FOR USER MANAGEMENT
     */
    public function primaryMobile(): HasOne {
        return $this->hasOne(UserMobile::class, 'user_id', 'id')->where(function ($q) {
            $q->where('type', UserMobile::$type_mobile);
            $q->where('is_primary', 1);
        });
    }
    public function primaryPhone(): HasOne {
        return $this->hasOne(UserMobile::class, 'user_id', 'id')->where(function ($q) {
            $q->where('type', UserMobile::$type_landLine);
            $q->where('is_primary', 1);
        });
    }

    public function phones(): HasMany {
        return $this->hasMany(UserMobile::class, 'user_id', 'id')
            ->where('type', UserMobile::$type_landLine);
    }

    public function mobiles(): HasMany {
        return $this->hasMany(UserMobile::class, 'user_id', 'id')
            ->where('type', UserMobile::$type_mobile);
    }

    public function personalInfo(): HasOne {
        return $this->hasOne(UserInfo::class, 'user_id', 'id');
    }

    public function company(): HasOneThrough {
        return $this->hasOneThrough(Entity::class, EntityUser::class,
            'user_id',
            'id', // entities.id
            'id',
            'entity_id'
        )
            ->select("entities.*", "entity_users.position")
            ->where('entity_type_id', Entity::$type_companyType);
    }

    public function unions(): BelongsToMany {
        return $this->belongsToMany(Entity::class, EntityUser::class,
            'user_id', // entity_users.user_id
            'entity_id' // entity_users.entity_id
        )->withPivot('position')
            ->where('entity_type_id', Entity::$type_unionType);
    }

    public function eventUser(): HasOne {
        return $this->hasOne(EventUser::class, 'user_id', 'id')->with('user');
    }

    public function userVisibility(): HasOne {
        return $this->hasOne(UserClmVisibility::class, 'user_id', 'id');
    }

    public function eventUsedTags(): BelongsToMany {
        return $this->belongsToMany(OrganiserTag::class,
            'organiser_tag_users',
            'user_id', // event_meta_tag.[foreignPivotKey] in (select result of users.[parentKey])
            'tag_id', //on event_tag.[relatedKey] = event_tag_meta.[relatedPivotKey]
            'id', // id of user table
            'id' //on event_tag.[relatedKey] = event_tag_meta.[relatedPivotKey]
        )
            ->where('is_display', 1)
            ->orderBy('name', 'asc');
    }

    public function tagsRelationForPP() {
        return $this->hasMany(UserTag::class, 'user_id', 'id');
    }


    /*
     * RELATIONS FOR KCT ADMIN
     */

    public function group(): HasOneThrough {
        return $this->hasOneThrough(
            Group::class,
            GroupUser::class,
            'user_id',
            'id',
            'id',
            'group_id'
        );
    }

    public function userMeta(): HasOne {
        return $this->hasOne(UserMetas::class, 'user_id','id');
    }

    public function otp(): HasOne {
        return $this->hasOne(OtpCode::class, 'user_id', 'id');
    }
}
