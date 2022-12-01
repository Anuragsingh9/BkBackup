<?php

namespace Modules\KctUser\Entities;

use App\Models\User;
use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\KctAdmin\Entities\EventSingleRecurrence;

class EventUserJoinReport extends TenantModel {
    use HasFactory;

    protected $fillable = [
        'event_uuid',
        'user_id',
        'on_leave', 'created_at',
        'recurrence_uuid',
    ];

    public function user(): HasOne {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function event():HasOne {
        return $this->hasOne(Event::class, 'event_uuid', 'event_uuid');
    }

}
