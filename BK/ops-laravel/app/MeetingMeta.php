<?php

namespace App;

use Hyn\Tenancy\Abstracts\TenantModel;

class MeetingMeta extends TenantModel {
    protected $table = 'meeting_meta';

    protected $fillable = [
        'meeting_id', 'video_meeting_id', 'video_meeting_numeric_id', 'video_meeting_user_id',
    ];

    protected $hidden = [
        'created_at', 'updated_at',
    ];

    public function meeting() {
        return $this->hasOne('App\Meeting', 'id', 'meeting_id');
    }

    public function blueJeansUser() {
        return $this->hasOne(UserBlueJeans::class , 'bluejeans_user_id', 'video_meeting_user_id');
    }
}
