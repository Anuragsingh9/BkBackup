<?php

namespace Modules\KctUser\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebhooksLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'webhook_type',
        'logs',
    ];

    protected $casts = [
        'logs' => "array",
    ];
}
