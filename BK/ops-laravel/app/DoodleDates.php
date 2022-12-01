<?php

namespace App;
// use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class DoodleDates extends TenancyModel
{
	
	function doodleVotes(){
		return $this->hasMany('App\DoodleVote','doodle_id','id');
	}
}
