<?php

namespace App;

//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class WorkshopMeta extends TenancyModel {

    protected $with = ['user:id,fname,lname,email,role,setting,role_commision,industry_id,phone,mobile,fcm_token'];
    protected $fillable = ['id', 'workshop_id', 'user_id', 'role','meeting_id'];

    
    public function user() {
        return $this->hasOne('App\User', 'id', 'user_id')->with('union');
    }

     public function union(){
      return $this->belongsTo('App\Union','union_id','id');
      } 

    public function doodleVote() {
        return $this->hasMany('App\DoodleVote', 'user_id', 'user_id');
    }
    public function workshop()
    {
        return $this->belongsTo('App\Workshop')->withoutGlobalScopes();
    }
}
