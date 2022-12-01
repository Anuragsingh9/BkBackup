<?php

namespace Modules\Crm\Entities;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

// use Illuminate\Database\Eloquent\Model;

class CrmFilterCondition extends TenancyModel
{
    protected $table = 'crm_filter_conditions';
    public $timestamps = true;
    protected $fillable = [
        'id',
        'filter_id',
        'component',
        'condition',
        'condition_type', // (and, or)
        'value',
        'field_default',
        'field_custom',
        'filter_type_id',
        'field_name',
        'is_default',
        'field_id',
    ];

    public function getConditionRule()
    {
        return $this->hasOne('Modules\Crm\Entities\CrmFilterRule', 'condition', 'short_name');
    }
}