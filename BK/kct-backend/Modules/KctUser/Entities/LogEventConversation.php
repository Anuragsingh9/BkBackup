<?php

namespace Modules\KctUser\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\KctAdmin\Entities\EventSingleRecurrence;
use Modules\KctAdmin\Entities\Space;

class LogEventConversation extends TenantModel
{
    use HasFactory;

    protected $fillable = [
        'rec_uuid',
        'space_uuid',
        'convo_uuid',
        'convo_start',
        'convo_end',
    ];

    public function space(): HasOne {
      return $this->hasOne(Space::class, 'space_uuid', 'space_uuid');
    }

    public function conversation(): HasOne {
        return $this->hasOne(Conversation::class, 'uuid', 'convo_uuid');
    }

    public function singleRecurrence(): HasOne {
        return $this->hasOne(EventSingleRecurrence::class, 'recurrence_uuid', 'rec_uuid');
    }

    public function conversationSubState(): HasMany {
        return $this->hasMany(LogConvoSubState::class,'convo_log_id','id');
    }

    public function conversationSubStateForDuration(): HasMany {
        return $this->hasMany(LogConvoSubState::class,'convo_log_id','id');
    }
}
