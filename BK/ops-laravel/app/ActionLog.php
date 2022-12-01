<?php

namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class ActionLog extends TenancyModel
{
	protected $fillable=['id', 'menu', 'sub_menu', 'action', 'user_id', 'ip_address'];
	function user(){
		return $this->hasOne('App\User','id','user_id');
	}
}
