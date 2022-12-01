<?php
namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class Token extends TenancyModel
{
	protected $fillable = ['user_id', 'api_token', 'remember_token', 'expired'];
	
}
