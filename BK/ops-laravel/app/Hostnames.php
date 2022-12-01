<?php

namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class Hostnames extends TenancyModel
{
	public function organisation()
	{

		return $this->belongsTo('App\Organisation','id','account_id');
	
	}
}
