<?php

namespace App\Model;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class UserTaskPermission extends TenancyModel
{
    public $fillable = [
        'task_id',
        'user_id',
        'workshop_id',
        'action_type',
        'project_id',
        'status',
    ];


    /**
     * Get the Task.
     */
    public function task()
    {
        return $this->belongsTo('App\Task', 'task_id');
    }

    /**
     * Get the User.
     */
   
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
