<?php

namespace Modules\KctUser\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LogUserData extends TenantModel
{
    use HasFactory;
    protected $casts = ['user_data' => 'array'];

    protected $fillable = ['user_id', 'user_data', 'event_uuid', 'conversation_uuid'];

    protected static function newFactory()
    {
        return \Modules\KctUser\Database\factories\LogUserDataFactory::new();
    }
}
