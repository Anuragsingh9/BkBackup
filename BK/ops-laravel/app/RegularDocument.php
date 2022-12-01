<?php

namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class RegularDocument extends TenancyModel
{
	protected $fillable=['workshop_id', 'event_id', 'message_category_id', 'user_id', 'created_by_user_id', 'document_title', 'document_type_id', 'document_file', 'issuer_id', 'is_active', 'increment_number', 'download_count', 'uncote'];
	
	function workshop(){
		return $this->hasOne('App\Workshop','id','workshop_id');
	}
	function documentType(){
		return $this->hasOne('App\DocumentType','id','document_type_id');
	}
	function issuer(){
		return $this->hasOne('App\Issuer','id','issuer_id');
	}
	function messageCategory(){
		return $this->hasOne('App\MessageCategory','id','message_category_id');
	}
	function user(){
		return $this->hasOne('App\User','id','user_id');
	}
}
