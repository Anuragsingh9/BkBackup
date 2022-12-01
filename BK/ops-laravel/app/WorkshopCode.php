<?php

namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class WorkshopCode extends TenancyModel
{
	public $fillable = array('workshop_id','code');
}
