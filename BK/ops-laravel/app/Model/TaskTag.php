<?php

namespace App\Model;

// use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class TaskTag extends TenancyModel
{
    protected $fillable=['task_id','tag_id'];
    
    public function tag()
    {
         return $this->hasOne('App\Model\Tags', 'id', 'tag_id');
    }
}
