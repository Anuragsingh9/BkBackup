<?php

namespace Modules\KctAdmin\Entities;

use App\Traits\UseUuid;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hyn\Tenancy\Abstracts\TenantModel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Modules\KctUser\Entities\EventSpaceUser;
use Modules\KctUser\Entities\EventUserJoinReport;
use Modules\KctUser\Entities\LogEventActionCount;
use Modules\KctUser\Entities\UserBan;

/**
 * @property string event_uuid
 * @property string title
 * @property ?string header_text
 * @property ?string header_line_1
 * @property ?string header_line_2
 * @property ?string description
 * @property string start_time
 * @property string end_time
 * @property ?string image
 * @property integer type
 * @property integer event_type
 * @property ?integer created_by_user_id
 * @property ?integer manual_opening
 * @property ?array event_settings
 * @property int is_mono_type
 * @property ?string $join_code
 *
 * Class Event
 * @package Modules\KctAdmin\Entities
 * @method static Event|null create($param)
 */
class Event extends TenantModel {
    use SoftDeletes, UseUuid;

    protected $primaryKey = 'event_uuid';

    public static int $type_networking = 1;
    public static int $type_content = 2;

    public static int $eventType_cafeteria = 1;
    public static int $eventType_executive = 2;
    public static int $eventType_manager = 3;
    public static int $eventType_all_day = 4; // water fountain type event

    protected $fillable = [
        'event_uuid',
        'title',
        'header_text',
        'header_line_1',
        'header_line_2',
        'description',
        'start_time',
        'join_code',
        'security_atr_id',
        'end_time',
        'image',
        'type', // 1. Networking, 2 Content
        'created_by_user_id',
        'manual_opening',
        'event_settings',
        'is_mono_type',
        'event_type', // 1.Cafeteria, 2.Executive, 3. Manager
    ];

    protected $casts = [
        'event_settings' => 'array',
    ];

    public function group(): HasOneThrough {
        return $this->hasOneThrough(Group::class,
            GroupEvent::class,
            'event_uuid', // group_events.event_uuid
            'id', // groups.id
            'event_uuid', // events.event_uuid
            'group_id' // group_events.group_id
        );
    }

    public function hosts() {
        return $this->morphToMany(User::class,
            'hostable',
            'event_hostables',
            'hostable_uuid',
            'host_id',
            'event_uuid'
        );
    }

    public function spaces(): HasMany {
        return $this->hasMany(Space::class, 'event_uuid', 'event_uuid')->orderBy('created_at');
    }

    public function organiser(): HasOne {
        return $this->hasOne(EventUser::class, 'event_uuid', 'event_uuid')
            ->where('is_organiser', 1);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the user who created the event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return HasOne
     */
    public function createdBy(): HasOne {
        return $this->hasOne(User::class,'id', 'created_by_user_id');
    }


    public function currentSpace() {
        return $this->hasOne(Space::class, 'event_uuid', 'event_uuid')
            ->with([
                'currentConversation',
                'singleUsers',
                'conversations',
                'conversations.users',
            ])
            ->whereHas('spaceUsers', function ($q) {
                $q->where('user_id', Auth::user()->id);
            })->orderBy(EventSpaceUser::select('current_conversation_uuid')
                ->whereColumn('kct_space_users.space_uuid', 'event_spaces.space_uuid')
                ->whereNotNull('current_conversation_uuid')
                ->where('user_id', Auth::user()->id)
                ->limit(1),
                'desc'
            );
    }

    public function eventUserRelation(): HasMany {
        return $this->hasMany(EventUser::class, 'event_uuid', 'event_uuid');
    }

    public function eventUsers(): BelongsToMany {
        return $this->belongsToMany(
            User::class,
            'event_users',
            'event_uuid',
            'user_id',
            'event_uuid',
            'id'
        )->withPivot('is_presenter', 'is_moderator','is_joined_after_reg');
    }

    public function isHostOfAnySpace() {
        $hostRole = function ($q) {
            $q->where('role', 1);
            $q->where('user_id', Auth::user()->id);
        };
        return $this->hasMany(Space::class, 'event_uuid', 'event_uuid')
            ->with(['spaceUsers' => $hostRole])
            ->whereHas('spaceUsers', $hostRole);
    }

    public function selfUserBanStatus(): HasOne {
        return $this->hasOne(UserBan::class, 'banable_id', 'event_uuid')
            ->where('user_id', Auth::user()->id);
    }

    public function banUser(): \Illuminate\Database\Eloquent\Relations\MorphMany {
        return $this->morphMany(UserBan::class, 'banable', 'banable_type', 'banable_id', 'event_uuid');
    }

    public function dummyRelations(): HasMany {
        return $this->hasMany(EventDummyUser::class, 'event_uuid', 'event_uuid');
    }

    public function moments(): HasMany {
        return $this->hasMany(Moment::class, 'event_uuid', 'event_uuid');
    }

    public function moderatorMoments() {
        return $this->hasMany(Moment::class, 'event_uuid', 'event_uuid')
            ->whereHas('moderator', function ($q) {
                $q->where('user_id', Auth::user()->id);
            });
    }

    public function speakerMoments() {
        return $this->hasMany(Moment::class, 'event_uuid', 'event_uuid')
            ->whereHas('speakers', function ($q) {
                $q->where('user_id', Auth::user()->id);
            });
    }

    public function draft(): HasOne {
        return $this->hasOne(EventMeta::class,'event_uuid','event_uuid');
    }

    public function eventUserRole(): HasMany {
        return $this->hasMany(EventUser::class, 'event_uuid', 'event_uuid')->where('user_id',Auth::user()->id);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This load relation of event to recurrence event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return HasOne
     */
    public function eventRecurrenceData(): HasOne {
        return $this->hasOne(EventRecurrences::class,'event_uuid','event_uuid');
    }
    public function isAdmin():HasOneThrough {
        return $this->hasOneThrough(GroupUser::class, GroupEvent::class,
            'event_uuid', // group_events.event_uuid
            'group_id', // group_users.group_id
            'event_uuid', // events.event_uuid
            'group_id' , // group_events.group_id
        )->where('user_id', Auth::user()->id)->whereIn('role', [2,3,4]);
    }

    public function eventJoinedReport(): HasMany {
        return $this->hasMany(EventUserJoinReport::class,'event_uuid','event_uuid');
    }

    public function eventRecurrenceRecord() {
        return $this->hasMany(EventSingleRecurrence::class, 'event_uuid', 'event_uuid');
    }

    public function actionLog(): HasManyThrough {
        return $this->hasManyThrough(
          LogEventActionCount::class,
          EventSingleRecurrence::class,
            'event_uuid',
            'recurrence_uuid',
            'event_uuid',
            'recurrence_uuid',
        );
    }


}
