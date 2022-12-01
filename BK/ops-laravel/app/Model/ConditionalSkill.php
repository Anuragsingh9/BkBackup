<?php

namespace App\Model;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class ConditionalSkill extends TenancyModel
{
    protected $fillable = [
        'conditional_field_id', 'conditional_checkbox_id','is_checked'
    ];
}
