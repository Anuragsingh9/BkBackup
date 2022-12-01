<?php
namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class TopicDocuments extends TenancyModel
{
	protected $with = 'docs';
	protected $guarded = ['id'];
	public function docs(){
        return $this->hasOne('App\RegularDocument','id','document_id');
    }
}
