<?php

namespace App;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserBlueJeans extends TenancyModel {
    use SoftDeletes;
    protected $table = 'user_bluejeans';
    protected $fillable = [
        'bluejeans_user_id',
        'fname',
        'lname',
        'email',
        'moderator_passcode',
    ];

    public function meetingMetas() {
        return $this->hasMany(MeetingMeta::class, 'video_meeting_user_id','bluejeans_user_id');
    }

}
