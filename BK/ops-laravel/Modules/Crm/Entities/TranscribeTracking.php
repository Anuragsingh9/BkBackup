<?php

namespace Modules\Crm\Entities;

use Illuminate\Database\Eloquent\Model;

class TranscribeTracking extends Model {
    protected $table = 'transcribe_logs';
    protected $fillable = ['account_id', 'user_id', 'time_used', 'track_type', 'type', 'used_at', 'used_at'];

    public function hostname() {
        return $this->hasOne('\Hyn\Tenancy\Models\Hostname', 'id', 'account_id');
    }
}
