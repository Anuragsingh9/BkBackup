<?php

namespace Modules\KctAdmin\Entities;

use App\Traits\UseUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hyn\Tenancy\Abstracts\TenantModel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Modules\KctUser\Entities\Conversation;
use Modules\KctUser\Entities\EventSpaceUser;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @property string space_uuid
 * @property string space_name
 * @property ?string space_short_name
 * @property ?string space_mood
 * @property integer max_capacity
 * @property integer is_vip_space
 * @property integer is_duo_space
 * @property string event_uuid
 * @property string order_id
 *
 * @property Event|null event
 * @method static find(Integer $id)
 * @method static create(array $array)
 *
 * Class KctSpace
 * @package Modules\KctAdmin\Entities
 */
class Space extends TenantModel {
    use HasFactory, SoftDeletes, UseUuid;

    protected $table = 'event_spaces';
    protected $primaryKey = 'space_uuid';

    protected $fillable = [
        'space_uuid', 'space_name', 'space_short_name', 'space_mood',
        'max_capacity', 'is_vip_space',
        'is_duo_space', 'event_uuid', 'order_id', 'is_mono_space',
    ];

    public static int $space_host = 1;
    public static int $member = 2;

    public function event(): HasOne {
        return $this->hasOne(Event::class, 'event_uuid', 'event_uuid');
    }

    public function hosts(): MorphToMany {
        return $this->morphToMany(User::class,
            'hostable',
            'event_hostables',
            'hostable_uuid',
            'host_id'
        );
    }

    public function singleUsers(): BelongsToMany {
        return $this->belongsToMany(User::class, 'kct_space_users', 'space_uuid', 'user_id', 'space_uuid', 'id')
            ->with(['eventUsedTags','tagsRelationForPP'])
            ->whereNull('kct_space_users.current_conversation_uuid')
            ->where('user_id', '!=', Auth::user()->id)
            ->orderBy('users.fname');
    }

    public function spaceUsers(): HasMany {
        return $this->hasMany(EventSpaceUser::class, 'space_uuid', 'space_uuid');
    }

    public function currentConversation() {
        $userRelation = function ($q) {
            $q->where('user_id', Auth::user()->id);
        };
        return $this->hasOne(Conversation::class, 'space_uuid', 'space_uuid')
            ->with([
                'userRelation' => $userRelation,
                'currentUser',
                'users',
            ])
            ->whereHas('userRelation', $userRelation);
    }

    public function conversations(): HasMany {
        return $this->hasMany(Conversation::class, 'space_uuid', 'space_uuid');
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will find the dummy user relation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return HasMany
     */
    public function dummyRelations(): HasMany {
        return $this->hasMany(EventDummyUser::class, 'space_uuid', 'space_uuid');
    }

    public function spaceHost(): HasOne {
        return $this->hasOne(EventSpaceUser::class,'space_uuid','space_uuid')
            ->where('role',self::$space_host)
            ->with('user');
    }

}
