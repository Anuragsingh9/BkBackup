<?php
namespace App;

use Auth;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class Task extends TenancyModel
{
    public $fillable = ['workshop_id',
                'task_created_by_id',
                'task_text',
                'milestone_id',
                'start_date',
                'end_date',
                'assign_for',
                'activity_type_id',
                'status',
                'task_color_id'];
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'assigned_to_id')->select(['id', 'fname', 'lname', 'email', 'mobile']);
    }

    public function createdBy()
    {
        return $this->hasOne('App\User', 'id', 'task_created_by_id')->select(['id', 'fname', 'lname', 'email', 'mobile']);
    }

    public function task_user()
    {
        return $this->hasMany('App\TaskUser', 'task_id', 'id');
    }

    public function workshop()
    {
        return $this->hasOne('App\Workshop', 'id', 'workshop_id')->select(['id', 'workshop_name', 'workshop_desc','code1']);
    }
    public function workshopRelate(){
        return $this->hasOne('App\Workshop', 'id', 'workshop_id')->select(['id','code1','workshop_name']);
    }
    public function activityType(){
        return $this->hasOne('App\Model\ActivityType', 'id', 'activity_type_id')->select(['id','svg','en_name','fr_name']);
    }
    public function milestone()
    {
        return $this->hasOne('App\Milestone', 'id', 'milestone_id');
    }

    public function task_user_info()
    {
        return $this->hasMany('App\TaskUser', 'task_id');
    }

    public function taskDependency()
    {
        return $this->hasMany('App\Model\TaskDependency', 'parent_id');
    }

    public function taskDependent()
    {
        return $this->hasMany('App\Model\TaskDependency', 'child_id');
    }
    public function taskComment()
    {
        return $this->hasMany('App\Model\TaskComment', 'task_id');
    }
    public function taskDocument()
    {
        return $this->hasMany('App\Model\TaskDocument', 'task_id');
    }

    public function taskPermission()
    {
        return $this->hasMany('App\Model\UserTaskPermission', 'task_id');
    }

    public function color()
    {
         return $this->hasOne('App\Color', 'id', 'task_color_id');
    }
    public function taskTag()
    {
         return $this->hasMany('App\Model\TaskTag', 'task_id')->select(['id','task_id','tag_id'])->with('tag:color_id,id,en_name,fr_name');
    }
    public function activityStatus(){
         return $this->hasOne('App\Model\ActivityStatus', 'id', 'status');
    }
    
    public function crmTask()
    {
        return $this->hasOne('Modules\Crm\Entities\CrmTask', 'id', 'task_id');
    }

    public function users() {
        return $this->morphedByMany('App\User', 'crm_object_tasksable', 'crm_object_tasks');
    }

    public function contacts() {
        return $this->morphedByMany('App\Model\Contact', 'crm_object_tasksable', 'crm_object_tasks');
    }
}
