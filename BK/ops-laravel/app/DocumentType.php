<?php

namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class DocumentType extends TenancyModel
{
	public $fillable = array('id','document_name','document_code');
}
