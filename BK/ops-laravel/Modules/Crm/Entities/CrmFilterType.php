<?php

namespace Modules\Crm\Entities;

// use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class CrmFilterType extends TenancyModel
{
    protected $table = 'crm_filter_types';
    protected $fillable = [
        'id',
        'name',
        'identifier',
        'fr_name',
        'component'
    ];

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->table;
    }

}