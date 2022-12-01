<?php

namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class Issuer extends TenancyModel
{
	public $fillable = array('id','issuer_name','issuer_code');
}
