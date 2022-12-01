<?php
namespace App;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class Role extends TenancyModel
{
			public $fillable = array('id','role_key','fr_text','eng_text','status');
}
