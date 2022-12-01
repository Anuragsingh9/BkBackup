<?php
namespace Modules\Newsletter\Entities;

use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class Project extends TenancyModel
{
    public $fillable = array(
        'project_label',
        'milestone_name',
        'wid',
        'color_id',
        'end_date',
        'is_default_project',
    );

}