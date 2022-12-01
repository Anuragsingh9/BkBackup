<?php
namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class Group extends TenancyModel
{
	public $fillable = array('id','group_name');
}
