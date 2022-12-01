<?php
namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class UnionAdmin extends TenancyModel
{
	protected $fillable=['id', 'union_id', 'admin_id'];
	protected $with=['user'];
	function user(){
		return $this->hasOne('App\User','id','admin_id');
	}
}
