<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class SuperadminSetting extends TenancyModel
{

	public $fillable = array('id','setting_key','setting_value');
	
}
