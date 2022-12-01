<?php

namespace Modules\Events\Entities;


use App\Presence;
use App\User;
use App\WorkshopMeta;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;
use Modules\Cocktail\Entities\EventSpace;
use Modules\Cocktail\Entities\EventUser;
use Modules\Events\Traits\HaveUuidColumn;

class Event extends TenancyModel {
    use SoftDeletes, HaveUuidColumn;
    
    // the use uuid is trait which makes the uuid column to treat as primary key and  also it generates the new uuid on time bases
    
    protected $casts = [
        'event_fields'       => 'array',
        'bluejeans_settings' => 'array',
    ];
    
    protected $uuidColumns = [
        'event_uuid',
    ];
    protected $table = 'event_info';
    protected $fillable = [
        'title', 'header_text', 'header_line_1', 'header_line_2', 'description', 'date',
        'start_time', 'end_time', 'address', 'city', 'image', 'type', 'workshop_id',
        'created_by_user_id', 'wp_post_id', 'organiser_type', 'territory_value',
        'event_uuid', 'bluejeans_settings', 'event_fields', 'bluejeans_id', 'kct_enabled',
        'manual_opening',
    ];
    
    public function workshop() {
        return $this->hasOne('App\Workshop', 'id', 'workshop_id')->withoutGlobalScopes();
    }
    
    public function users() {
        return $this->morphedByMany('App\User', 'eventable');
    }
    
    public function organisers() {
        return $this->morphedByMany('Modules\Events\Entities\Organiser', 'eventable');
    }
    
    public function spaces() {
        return $this->hasMany(EventSpace::class, 'event_uuid', 'event_uuid');
    }
    
    public function eventUsers() {
        return $this->belongsToMany(
            User::class,
            'event_user_data',
            'event_uuid',
            'user_id',
            'event_uuid',
            'id'
        )->withPivot('is_presenter', 'is_moderator');
    }
    
    public function currentSpace() {
        return $this->hasOne(EventSpace::class, 'event_uuid', 'event_uuid')
            ->with([
                'currentConversation',
                'singleUsers',
                'conversations',
                'conversations.users',
            ])
            ->whereHas('spaceUsers', function ($q) {
                $q->where('user_id', Auth::user()->id);
            });
    }
    
    public function isWorkshopAdmin() {
        return $this->workshop->meta()->whereIn('role', [1, 2])->where('user_id', Auth::user()->id)->count();
    }
    
    public function isHostOfAnySpace() {
        $hostRole = function ($q) {
            $q->where('role', 1);
            $q->where('user_id', Auth::user()->id);
        };
        return $this->hasMany(EventSpace::class, 'event_uuid', 'event_uuid')
            ->with(['spaceUsers' => $hostRole])
            ->whereHas('spaceUsers', $hostRole);
    }
    
    /**
     * @return HasMany
     */
    public function eventUserRelation() {
        return $this->hasMany(EventUser::class, 'event_uuid', 'event_uuid');
    }
    
    public function presences() {
        return $this->belongsToMany(Presence::class, 'workshops', 'id', 'id', 'workshop_id', 'workshop_id');
    }
    
    public function defaultSpace() {
        return $this->hasOne(EventSpace::class, 'event_uuid', 'event_uuid')->orderBy('created_at');
    }
    
    public function isCurrentUserMember() {
        return $this->belongsToMany(
            WorkshopMeta::class,
            'workshops',
            'id',
            'id',
            'workshop_id',
            'workshop_id'
        )->where('user_id', Auth::user()->id);
    }
    
    
    public function secretory() {
        return $this->hasOne(WorkshopMeta::class, 'workshop_id', 'workshop_id')
            ->where('role', 1)
            ->whereHas('user');
    }
    
    public function deputy() {
        return $this->hasOne(WorkshopMeta::class, 'workshop_id', 'workshop_id')
            ->where('role', 2)
            ->whereHas('user');
    }
    
}
