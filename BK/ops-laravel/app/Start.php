<?php

namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class Start extends TenancyModel
{
	protected $fillable=[ 'id', 'start_category_id', 'title_fr', 'title_en', 'url', 'status', 'sort_order'];
	
}
