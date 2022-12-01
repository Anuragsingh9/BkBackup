<?php

namespace Modules\KctUser\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LogEventContent extends TenantModel {
    use HasFactory;

    public static $action_zoom = 1;
    public static $action_video = 2;
    public static $action_image = 3;
    public static $action_network = 4;
    public static $action_networkMute = 5;
    public static $action_content = 6;


    protected $fillable = [
        'recurrence_uuid',
        'action',
        'action_state',
        'start_time',
        'duration',
    ];
}
