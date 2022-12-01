<?php

namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class ResourcesCategory extends TenancyModel
{
	public $fillable = array('id','category_name','category_desc','parent','resources_type','group_id','is_public','is_private');
	function resources()
    {
        return $this->hasMany('App\Resources','resources_category_id','id');
    }
}
