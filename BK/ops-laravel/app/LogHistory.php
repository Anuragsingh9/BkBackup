<?php
namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class LogHistory extends TenancyModel
{
	protected $table="log_history";
	protected $guarded = ['id'];
	protected $fillable = ['ip','action','user_id','updated_at'];
}
