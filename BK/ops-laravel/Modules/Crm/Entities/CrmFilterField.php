<?php

namespace Modules\Crm\Entities;

// use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

use Hyn\Tenancy\Traits\UsesTenantConnection;

class CrmFilterField extends TenancyModel
{
    protected $table = 'crm_filter_fields';
    protected $fillable = [
        'id',
        'filter_id',
        'value'
    ];

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->table;
    }

}