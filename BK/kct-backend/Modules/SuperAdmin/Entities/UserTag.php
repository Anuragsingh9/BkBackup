<?php

namespace Modules\SuperAdmin\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\SuperAdmin\Database\factories\UserTagFactory;

/**
 * @property int id
 * @property int tag_type
 * @property int status
 *
 * Class UserTag
 * @package Modules\SuperAdmin\Entities
 */
class UserTag extends Model {
    use HasFactory;

    protected $fillable = [
        'tag_type', // 1. Professional, 2. Personal
        'status', // 1. Accepted, 2. Rejected, 3. Pending
    ];

    protected static function newFactory(): UserTagFactory {
        return UserTagFactory::new();
    }

    public function locales(): HasMany {
        return $this->hasMany(UserTagLocale::class, 'tag_id', 'id');
    }

}
