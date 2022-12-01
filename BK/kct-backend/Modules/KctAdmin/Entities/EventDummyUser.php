<?php

namespace Modules\KctAdmin\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\KctUser\Entities\Conversation;
use Modules\UserManagement\Entities\DummyUser;

/**
 * @property string event_uuid
 * @property string space_uuid
 * @property integer dummy_user_id
 * @property string current_conv_uuid
 *
 * Class KctDummyUsers
 * @package Modules\KctAdmin\Entities
 */
class EventDummyUser extends TenantModel {
    use HasFactory;

    protected $table = 'event_dummy_users';
    protected $fillable = ['event_uuid', 'space_uuid', 'dummy_user_id', 'current_conv_uuid'];

    public function dummyUsers(): BelongsTo {
        return $this->belongsTo(DummyUser::class, 'dummy_user_id', 'id');
    }

    public function dummyUser(): BelongsTo {
        return $this->belongsTo(DummyUser::class, 'dummy_user_id', 'id');
    }

    public function conversation(): BelongsTo {
        return $this->belongsTo(Conversation::class, 'current_conv_uuid', 'uuid');
    }

    public function event(): BelongsTo {
        return $this->belongsTo(Event::class, 'event_uuid', 'event_uuid');
    }
}
