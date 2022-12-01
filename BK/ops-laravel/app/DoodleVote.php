<?php

namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class DoodleVote extends TenancyModel
{
	function doodleDates(){
		return $this->hasOne('App\DoodleDates','id','doodle_id');
	}
	function user(){
		return $this->hasOne('App\User','id','user_id');
	}
}
