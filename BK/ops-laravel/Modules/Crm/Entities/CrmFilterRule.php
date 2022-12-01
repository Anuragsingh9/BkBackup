<?php

namespace Modules\Crm\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;


class CrmFilterRule extends TenancyModel
{
    protected $table = 'crm_filter_rules';
    protected $fillable = [
        'id',
        'name',
        'short_name',
        'operator',
        'value',
        'fr_name',
        'available_formats',
    ];


}