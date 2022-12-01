<?php

namespace Modules\Cocktail\Entities;

use App\Traits\HaveUuidColumn;
use App\Traits\UseUuid;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Support\Facades\Auth;
use Modules\Events\Entities\Event;

class EventSpace extends TenancyModel {
    
    use SoftDeletes, UseUuid;
    
    protected $table = 'event_space';
    protected $primaryKey = 'space_uuid';
    protected $uuidColumns = ['event_uuid', 'space_uuid'];
    
    protected $casts = [
        'opening_hours' => 'array'
    ];
    
    protected $fillable = [
        'space_uuid', 'space_name', 'space_short_name', 'space_mood',
        'max_capacity', 'space_image_url', 'space_icon_url', 'is_vip_space',
        'opening_hours', 'event_uuid', 'tags', 'follow_main_opening_hours',
        'order_id', 'is_duo_space',
    ];
    
    public function event() {
        return $this->hasOne(Event::class, 'event_uuid', 'event_uuid');
    }
    
    
    public function spaceUsers() {
        return $this->hasMany(EventSpaceUser::class, 'space_uuid', 'space_uuid');
    }

    public function spaceDummyUsers(){
        return $this->hasMany(EventDummyUser::class,'space_uuid','space_uuid');
    }
    
    public function hosts() {
        return $this->belongsToMany(
            User::class,
            'event_space_users',
            'space_uuid',
            'user_id',
            'space_uuid'
        )->where('event_space_users.role', EventSpaceUser::$ROLE_HOST);
    }
    
    public function conversations() {
        return $this->hasMany(Conversation::class, 'space_uuid', 'space_uuid');
    }
    
    public function singleUsers() {
        return $this->belongsToMany(User::class, 'event_space_users', 'space_uuid', 'user_id', 'space_uuid', 'id')
            ->with('eventUsedTags', 'tagsRelationForPP')
            ->whereNull('event_space_users.current_conversation_uuid')
            ->where('user_id', '!=', Auth::user()->id)
            ->orderBy('users.fname');
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
    
    public function dummyRelations() {
        return $this->hasMany(EventDummyUser::class, 'space_uuid', 'space_uuid');
    }
}