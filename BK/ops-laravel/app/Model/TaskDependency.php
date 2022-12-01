<?php

namespace App\Model;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class   TaskDependency extends TenancyModel
{
    public $fillable = [
        'parent_id',
        'child_id',
        'created_by_id',
    ];

    /**
     * Get the parentTask.
     */
    public function dependent()
    {
        return $this->belongsTo('App\Task', 'parent_id')->withDefault();
    }


    /**
     * Get the childTask.
     */
    public function dependency()
    {
        return $this->belongsTo('App\Task', 'child_id')->withDefault();
    }

    public function taskDependency()
    {
        return $this->belongsTo('App\Task', 'child_id')->withDefault();
    }

    public function taskDependent()
    {
        return $this->belongsTo('App\Task', 'child_id')->withDefault();
    }
}
