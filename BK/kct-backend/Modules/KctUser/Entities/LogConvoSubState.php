<?php

namespace Modules\KctUser\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LogConvoSubState extends TenantModel
{
    use HasFactory;

    protected $fillable = [
        'convo_log_id',
        'users_count',
        'start_time',
        'end_time',
        'duration',
    ];

    protected $table = 'log_convo_sub_state';


    public function logEventConversation(): HasMany {
        return $this->hasMany(LogEventConversation::class, 'id', 'convo_log_id');
    }

}
