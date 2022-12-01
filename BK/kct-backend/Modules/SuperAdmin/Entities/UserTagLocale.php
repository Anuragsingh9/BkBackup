<?php

namespace Modules\SuperAdmin\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int tag_id
 * @property string locale
 * @property string value
 *
 * Class UserTagLocale
 * @package Modules\SuperAdmin\Entities
 */
class UserTagLocale extends Model {

    protected $fillable = [
        'tag_id',
        'locale',
        'value',
    ];

    public function tag() {
        $this->belongsTo(UserTag::class, 'id', 'tag_id');
    }

}
