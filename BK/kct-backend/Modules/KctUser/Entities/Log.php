<?php

namespace Modules\KctUser\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Log extends TenantModel
{
    use HasFactory;

    protected $fillable = ['current_browser', 'ip_address', 'log_type'];

    protected static function newFactory()
    {
        return \Modules\KctUser\Database\factories\LogsFactory::new();
    }

    public function logUserData(): HasOne {
        return $this->hasOne(LogUserData::class, 'log_id', 'id');
    }
}
