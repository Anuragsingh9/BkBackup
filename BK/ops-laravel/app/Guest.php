<?php

namespace App;

//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class Guest extends TenancyModel {

    public $fillable = array('id', 'email', 'workshop_id', 'meeting_id');

    function doodleVote() {
        return $this->hasMany('App\DoodleVote', 'guest_id', 'id');
    }

    function user() {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

}
