<?php

namespace Modules\UserManagement\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\KctAdmin\Entities\EventDummyUser;

/**
 * @property string fname
 * @property string lname
 * @property string avatar
 * @property string company
 * @property string company_position
 * @property string union
 * @property string union_position
 * @property string video_url
 * @property integer type
 *
 * @method static create(array $array)
 * @method static insert(array $dataToInsert)
 */
class DummyUser extends TenantModel {

    protected $fillable = [
        'fname',
        'lname',
        'avatar',
        'company',
        'company_position',
        'union',
        'union_position',
        'video_url',
        'type', // 1 regular
    ];

    public function conversations(): HasOne {
        return $this->hasOne(EventDummyUser::class, 'dummy_user_id', 'id');
    }
}
