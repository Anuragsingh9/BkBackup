<?php

namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class StartCategory extends TenancyModel
{
	
	public function start(){
		return $this->hasMany('App\Start','start_category_id','id')->orderBy('sort_order','ASC');
	}
}
