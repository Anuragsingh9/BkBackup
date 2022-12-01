<?php

namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class WikiEditor extends TenancyModel
{

	public $fillable = array('id','editor_id','wiki_id','added_by');
	public function user(){
        return $this->hasOne('App\User','id','editor_id')->select('id','fname','lname','email');
    }
    public function wiki()
    {
    	return $this->hasOne('App\Wiki','id','wiki_id')->select('*');
    }
}
