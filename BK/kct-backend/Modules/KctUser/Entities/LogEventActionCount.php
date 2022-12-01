<?php

namespace Modules\KctUser\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Modules\KctAdmin\Entities\Event;
use Modules\KctAdmin\Entities\EventSingleRecurrence;

class LogEventActionCount extends TenantModel {
    use HasFactory;

    protected $fillable = [
        'group_id',
        'recurrence_uuid',
        'conv_count',
        'reg_count',
        'attendee_count',
        'p_image_count',
        'p_video_count',
        'p_zoom_count',
        'sh_conv_count',
    ];

    public function singleRecurrence(): HasOne {
        return $this->hasOne(EventSingleRecurrence::class, 'recurrence_uuid', 'recurrence_uuid');
    }

    public function event(): HasOneThrough {
        return $this->hasOneThrough(
            Event::class,
            EventSingleRecurrence::class,
            'recurrence_uuid',
            'event_uuid',
            'recurrence_uuid',
            'event_uuid',
        );
    }
}
