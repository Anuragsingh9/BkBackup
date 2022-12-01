<?php

namespace Modules\Cocktail\Entities;

use App\Presence;
use App\Traits\HaveUuidColumn;
use App\Traits\UseUuid;
use App\User;
use App\WorkshopMeta;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Modules\Events\Entities\Event;

class EventUser extends TenancyModel {
    
    protected $table = 'event_user_data';
    
    
    protected $fillable = [
        'event_uuid', 'user_id', 'is_presenter', 'is_moderator', 'state', 'is_joined_after_reg',
    ];
    
    public function event() {
        return $this->belongsTo(Event::class, 'event_uuid', 'event_uuid');
    }
    
    public function isHost() {
        return $this->hasMany(EventSpaceUser::class, 'user_id', 'user_id')
            ->where('role', 1);
    }
    
    public function user() {
        return $this->hasOne(User::class, 'id', 'user_id')
            ->with(['unions', 'companies', 'instances', 'presses']);
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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function presenceStatus() {
        return $this->hasOne(Presence::class, 'user_id', 'user_id');
    }
    
}