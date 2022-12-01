<?php

namespace Modules\KctAdmin\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @property int id
 * @property string moment_name
 * @property string moment_description
 * @property array moment_settings
 * @property string start_time
 * @property string end_time
 * @property int moment_type
 * @property string event_uuid
 * @property int moment_id
 * @property int|null is_live
 *
 * Class Moment
 *
 * @package Modules\KctAdmin\Entities
 * @method static Moment|null create($param)
 */
class Moment extends TenantModel {
    use SoftDeletes;
    use HasFactory;

    public static int $momentType_networking = 1;
    public static int $momentType_defaultWebinar = 2;
    public static int $momentType_webinar = 3;
    public static int $momentType_meeting = 4;
    public static int $momentType_youtube = 5;
    public static int $momentType_vimeo = 6;

    protected $table = 'event_moments';
    protected $fillable = [
        'moment_name',
        'moment_description',
        'moment_settings',
        'start_time',
        'end_time',
        'moment_type',
        'event_uuid',
        'moment_id',
        'is_live',
    ];
    protected $casts = [
        'moment_settings' => 'array',
    ];

    public function event() {
        return $this->belongsTo(Event::class, 'event_uuid', 'event_uuid');
    }

    public function moderator(): HasOneThrough {
        return $this->hasOneThrough(
            EventUser::class,
            EventUserRole::class,
            'moment_id', // event_user_roles.moment_id
            'id', // event_users.id
            'id', // moments.id
            'event_user_id', // event_user_roles.event_user_id
        )->where('role', EventUserRole::$role_moderator);
    }

    public function speakers(): HasManyThrough {
        return $this->hasManyThrough(
            EventUser::class,
            EventUserRole::class,
            'moment_id', // event_user_roles.moment_id
            'id', // event_users.id
            'id', // moments.id
            'event_user_id', // event_user_roles.event_user_id
        )->where('role', EventUserRole::$role_speaker);
    }

}
