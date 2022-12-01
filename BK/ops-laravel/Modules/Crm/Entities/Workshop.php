<?php

namespace Modules\Newsletter\Entities;

// use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class Workshop extends TenancyModel
{
    public $fillable = array('validator_id','workshop_name','workshop_desc','code1','code2','workshop_type','president_id','is_private');
	// public function meta(){
    //     return $this->hasMany('App\WorkshopMeta');
    // }
  

}
