<?php

namespace Modules\KctAdmin\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property integer id
 * @property ?string event_uuid
 * @property ?string reg_start_time
 * @property ?string reg_end_time
 * @property ?int event_status
 * @property ?int share_agenda
 * @property ?int is_reg_open
 */
class EventMeta extends TenantModel
{
    use HasFactory;

    public static int $eventStatus_live = 1;
    public static int $eventStatus_draft = 2;

    public static int $event_regIsClose = 0;
    public static int $event_regIsOpen = 1;
    protected $fillable = [
        'event_uuid',
        'reg_start_time',
        'reg_end_time',
        'event_status',
        'share_agenda',
        'is_reg_open'
    ];

    public function event(): HasOne {
        return $this->hasOne(Event::class,'event_uuid','event_uuid');
    }

    protected static function newFactory()
    {
        return \Modules\KctAdmin\Database\factories\EventMetaFactory::new();
    }
}
