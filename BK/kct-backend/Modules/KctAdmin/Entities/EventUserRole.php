<?php

namespace Modules\KctAdmin\Entities;


use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Relations\HasOne;


/**
 * @property int id
 * @property string event_uuid
 * @property int event_user_id
 * @property int|null role
 * @property int|null moment_id
 * @property string space_uuid'
 * @property string created_at
 * @property string updated_at
 */
class EventUserRole extends TenantModel {
    use HasFactory;

    public static int $role_moderator = 3;
    public static int $role_speaker = 4;

    protected $fillable = [
        'event_user_id',
        'role', // 3 moderator, 4 speaker
        'moment_id',
        'space_uuid',
    ];

}
