<?php

namespace App\Model;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

//use Illuminate\Database\Eloquent\Model;

class SelectOption extends TenancyModel
{

    protected $fillable =
    [
    	 'sort_order',
        'option_value',
        'skill_id',
    ];
}
