<?php

namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class Union extends TenancyModel
{
	protected $fillable = array('id', 'union_name', 'union_code', 'logo', 'union_description', 'family_id', 'industry_id', 'address1', 'address2', 'postal_code', 'city', 'country', 'telephone', 'fax', 'email', 'website', 'contact_button', 'union_type', 'is_internal');

	function unionContacts(){
		return $this->hasMany('App\UnionContact','union_id','id');
	}
	function unionAdmin(){
		return $this->hasMany('App\UnionAdmin','union_id','id');
	}
	function industry(){
		return $this->hasOne('App\Industry','id','industry_id');
	}
		public function getFaxAttribute($value)
	{
		return ($value == 'null' || $value == null) ? '' : $value;
	}
	public function getTelephoneAttribute($value)
	{
		return ($value == 'null' || $value == null) ? '' : $value;
	}
}
