<?php

namespace Modules\KctAdmin\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * @property string name
 * @property integer is_display
 * @property integer created_by
 * @method static orderBy(string $string, string $string1)
 */
class OrganiserTag extends TenantModel {
    use HasFactory;

    protected $table = 'organiser_tags';

    protected $fillable = [
        'name',
        'created_by',
        'is_display',
    ];

    public function user(): HasOne {
        return $this->hasOne('App\User', 'id', 'user_id')->select(['id', 'fname', 'lname', 'email', 'mobile', 'avatar']);
    }

    public function group(): HasOneThrough {
        return $this->hasOneThrough(Group::class, GroupTag::class,
            'tag_id',
            'id',
            'id',
            'group_id'
        );
    }
}
