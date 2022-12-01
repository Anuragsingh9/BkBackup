<?php

namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class Timeline extends TenancyModel
{	
	protected $fillable=['workshop_id', 'type', 'description', 'action', 'user_id'];
	public function user(){
        return $this->hasOne('App\User','id','user_id')->select(['id','fname','lname']);
    }
}
