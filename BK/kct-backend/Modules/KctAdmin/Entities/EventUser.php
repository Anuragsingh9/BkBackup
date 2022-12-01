<?php

namespace Modules\KctAdmin\Entities;


use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\KctUser\Entities\EventSpaceUser;
use Modules\KctUser\Entities\EventUserJoinReport;


/**
 * @property string event_uuid
 * @property integer user_id
 * @property integer event_user_role
 * @property integer is_presenter
 * @property integer is_moderator
 * @property integer is_vip
 * @property string state
 * @property integer is_joined_after_reg
 * @property integer presence
 * @method static updateOrCreate(array $array, array $array1)
 *
 * Class EventUser
 * @package Modules\KctAdmin\Entities
 */
class EventUser extends TenantModel {
    use HasFactory;

    protected $table = 'event_users';

    public static int $team_member = 1;
    public static int $expert_member = 2;

    protected $fillable = [
        'event_uuid', 'user_id', 'event_user_role', // team or expert
        'is_presenter', 'is_moderator', 'is_vip', 'state', 'is_joined_after_reg',
        'is_organiser',
        'presence', // 1 absent, 2 present
    ];

    public function user(): HasOne {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function eventUserRole(): HasMany {
        return $this->hasMany(EventUserRole::class,'event_user_id', 'id');
    }

    public function eventUserJoinReport(): HasMany {
        return $this->hasMany(EventUserJoinReport::class, 'user_id', 'user_id');
    }

    public function event() {
        return $this->belongsTo(Event::class, 'event_uuid', 'event_uuid');
    }

    public function isHost(): HasMany {
        return $this->hasMany(EventSpaceUser::class, 'user_id', 'user_id')
            ->where('role', 1)
            ->whereHas('space');
    }


    /**
     * The workshop id to be provided when using
     *
     * @return HasMany
     */
    public function isSecretory() {
        return $this->hasMany(WorkshopMeta::class, 'user_id', 'user_id')
            ->where('role', 1); // president
    }


    /**
     * Add the workshop id when calling to get for specific event/workshop
     *
     * @return HasMany
     */
    public function isDeputy() {
        return $this->hasMany(WorkshopMeta::class, 'user_id', 'user_id')
            ->where('role', 2); // validator
    }

    /**
     * Pass the workshop id in case of event as there is only one workshop and one meeting
     * or pass the meeting id if there is more than one meeting in workshop
     *
     * @return HasOne
     */
    public function presenceStatus() {
        return $this->hasOne(Presence::class, 'user_id', 'user_id');
    }

    public function isEventHost() {
        return $this->hasOne(EventHostable::class, 'host_id', 'user_id')
            ->where('hostable_type', Event::class);
    }

    public function isSpaceHost() {
        return $this->hasOne(EventHostable::class, 'host_id', 'user_id')
            ->where('hostable_type', EventSpace::class);
    }

    public function hostSpaces(): HasMany {
        return $this->hasMany(Hostable::class, 'host_id', 'user_id')
            ->where('hostable_type', Space::class)->with('spaces');
    }

    public function userBans() {
        return $this->hasOne(UserBan::class, 'user_id', 'user_id');
    }

}
