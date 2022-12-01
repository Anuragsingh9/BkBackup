<?php

namespace Modules\KctUser\Entities;

use App\Models\User;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\KctAdmin\Entities\Space;

class EventSpaceUser extends TenancyModel {
    public static int $ROLE_HOST = 1;
    public static int $ROLE_MEMBER = 2;

    protected $table = 'kct_space_users';
    protected $fillable = ['space_uuid', 'user_id', 'role', 'current_conversation_uuid'];

    public function event() {
        return $this->space()->event;
    }

    public function space(): HasOne {
        return $this->hasOne(Space::class, 'space_uuid', 'space_uuid');
    }

    public function conversation(): HasOne {
        return $this->hasOne(Conversation::class, 'uuid', 'current_conversation_uuid');
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
