<?php

namespace App\Model;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

//use Illuminate\Database\Eloquent\Model;

class SkillImage extends TenancyModel
{

    protected $fillable =
    [
        'url',
        'text_before_link',
        'text_after_link',
        'text_of_link',
        'target_blank',
        'skill_id',
    ];
}
