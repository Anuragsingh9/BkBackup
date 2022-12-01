<?php

namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class Resources extends TenancyModel
{
	protected $fillable=['id', 'resources_name', 'resources_file', 'resources_category_id'];
    function cate()
    {
        return $this->hasMany('App\ResourcesCategory','id','resources_category_id');
    }
   /* function cate2()
    {
        return $this->hasMany('App\ResourcesCategory','id','resources_category_id')->wherIn('resources_category_id');
    }*/
}
