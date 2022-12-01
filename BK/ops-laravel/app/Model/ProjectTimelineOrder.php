<?php

namespace App\Model;

// use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class ProjectTimelineOrder extends TenancyModel
{
    public $fillable = ['wid', 'user_id', 'project_id','order'];
    protected $table = 'project_timeline_orders';
}
