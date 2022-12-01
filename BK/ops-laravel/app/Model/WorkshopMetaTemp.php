<?php

namespace App\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class WorkshopMetaTemp extends TenancyModel
{
    protected $with = ['user'];
    protected $fillable = ['id', 'workshop_id', 'user_id', 'role'];

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id')->with('union');
    }

    public function union()
    {
        return $this->belongsTo('App\Union', 'union_id', 'id');
    }

    public function doodleVote()
    {
        return $this->hasMany('App\DoodleVote', 'user_id', 'user_id');
    }
    public function workshop()
    {
        return $this->belongsTo('App\Workshop');
    }
}
