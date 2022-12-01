<?php

namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class WikiCategory extends TenancyModel
{
	public $fillable = array('id','category_name','category_desc');
	function wikis()
    {
        return $this->hasMany('App\Resources','resources_category_id','id');
    }
}
