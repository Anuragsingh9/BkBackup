<?php

namespace App\Model;

// use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class Tags extends TenancyModel
{
    public $fillable = ['color_id', 'fr_name','en_name','preview','description','type'];
    protected $with = ['color'];
    public function color(){
       return $this->hasOne('App\Color','id','color_id')->select(['id','code']);
   }
}
