<?php
namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class Topic extends TenancyModel
{
	protected $guarded = ['id'];
	public function docs(){
        return $this->hasMany('App\TopicDocuments');
    }
    public function notes(){
        return $this->hasOne('App\TopicNote');
    }
   
}
