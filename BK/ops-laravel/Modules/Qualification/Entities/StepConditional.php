<?php

namespace Modules\Qualification\Entities;

use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class StepConditional extends TenancyModel
{
    protected $fillable = [
        'step_id',
        'conditional_checkbox_id',
        'is_checked',
    ];
    protected $table = 'steps_conditional';

}
