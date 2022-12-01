<?php

namespace Modules\KctAdmin\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventRecurrences extends TenantModel {
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'event_uuid',
        // @note 2 and 4 are deprecated they should be handled logically by 3 and 5 type respectively
        'recurrence_type', // 1. Daily, 2. Weekdays, 3. Weekly, 4. Bi Monthly, 5. Monthly
        'end_date',
        'start_date',
        'recurrences_settings'
    ];

    protected $casts = [
        'recurrences_settings' => 'array',
    ];

    protected static function newFactory() {
        return \Modules\KctAdmin\Database\factories\EventRecurrencesFactory::new();
    }

    public function event() {
        return $this->belongsTo(Event::class, 'event_uuid', 'event_uuid');
    }
}
