<?php

namespace Modules\KctAdmin\Entities;

use App\Traits\UseUuid;
use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\KctUser\Entities\EventUserJoinReport;
use Modules\KctUser\Entities\LogEventActionCount;
use Modules\KctUser\Entities\LogEventConversation;

class EventSingleRecurrence extends TenantModel {
    use UseUuid;
    use HasFactory;

    protected $primaryKey = 'recurrence_uuid';

    protected $fillable = [
        'recurrence_uuid',
        'event_uuid',
        'recurrence_count',
        'recurrence_date',
    ];

    public function actionLog(): HasOne {
        return $this->hasOne(LogEventActionCount::class, 'recurrence_uuid', 'recurrence_uuid');
    }

    public function event(): BelongsTo {
        return $this->belongsTo(Event::class, 'event_uuid', 'event_uuid');
    }

    public function eventConversationLog(): HasMany {
        return $this->hasMany(LogEventConversation::class,'rec_uuid','recurrence_uuid');
    }

    public function userJoinReports(): HasMany {
        return $this->hasMany(EventUserJoinReport::class, 'recurrence_uuid', 'recurrence_uuid')->whereHas('user');
    }
}
