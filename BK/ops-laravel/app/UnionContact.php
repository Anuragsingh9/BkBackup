<?php
namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class UnionContact extends TenancyModel
{
	protected $fillable=['id', 'union_id', 'f_name', 'l_name', 'position', 'display', 'photo'];
}
