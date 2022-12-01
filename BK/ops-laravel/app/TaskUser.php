<?php
namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class TaskUser extends TenancyModel
{
	protected $with='user';
	protected $fillable=['user_id','task_id','task_status','task_date_completed'];
	public function user(){
        return $this->hasOne('App\User', 'id', 'user_id')->select(['id', 'fname', 'lname', 'email', 'mobile', 'avatar']);
	}
}
