<?php

namespace App\Model;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

//use Illuminate\Database\Eloquent\Model;

class SkillMeta extends TenancyModel
{

    protected $fillable =
    [
        'value',
        'skill_id',
    ];
}
