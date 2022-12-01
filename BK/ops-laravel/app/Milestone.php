<?php
namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class Milestone extends TenancyModel{

    public $fillable = array(
        'project_id',
        'label',
        'user_id',
        'end_date',
        'start_date',
        'color_id',
        'is_default_milestone',
    );

    public function color()
    {
        return $this->hasOne('App\Color', 'id', 'color_id');
    }
    public function projects(){
		return $this->hasOne('App\Project','id','project_id');
	}

    public function tasks()
    {
        return $this->hasMany('App\Task', 'milestone_id', 'id')->select('id','status','milestone_id','assign_for');
    }

    public function doneTasks()
    {
        return $this->hasMany('App\Task', 'milestone_id', 'id')->where('status',3)->select('id','status','milestone_id');
    }
}