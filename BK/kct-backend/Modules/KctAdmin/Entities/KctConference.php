<?php

namespace Modules\KctAdmin\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string event_uuid
 * @property integer conference_id
 * @property string conference_settings
 * @property integer is_active
 * @property integer conference_type
 * @property integer conference_time_block
 * @method static insert(array $array)
 *
 * Class KctConference
 * @package Modules\KctAdmin\Entities
 */
class KctConference extends TenantModel {
    use SoftDeletes;

    protected $table = 'event_conferences';
    protected $fillable = [
        'event_uuid', 'conference_id', 'conference_settings', 'is_active',
        'conference_type', 'conference_time_block',
    ];

    protected $casts = [
        'conference_settings' => 'array',
    ];

    public function event() {
        return $this->belongsTo(Event::class, 'event_uuid', 'event_uuid');
    }
}
