<?php

namespace App\Model;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class ActivityType extends TenancyModel
{
    public $fillable = array(
        'id',
        'en_name',
        'fr_name',
        'svg',
        );
}
