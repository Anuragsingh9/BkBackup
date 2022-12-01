<?php

namespace Modules\Crm\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class CrmTask extends TenancyModel
{
    protected $table = "crm_object_tasks";

    protected $fillable = ['task_id', 'crm_object_tasksable_id', 'crm_object_tasksable_type'];

    /**
     * Get all of the owning task_userable models.
     */
    public function crm_object_tasksable()
    {
        return $this->morphTo();
    }

    public function task()
    {
        return $this->hasOne('App\Task', 'id', 'task_id')/*->select(['id', 'fname', 'lname', 'email', 'mobile', 'avatar'])*/
            ;
    }
}
