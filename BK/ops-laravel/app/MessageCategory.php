<?php

namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class MessageCategory extends TenancyModel
{
	
	public $fillable = array('id','category_name','workshop_id','status');
}
