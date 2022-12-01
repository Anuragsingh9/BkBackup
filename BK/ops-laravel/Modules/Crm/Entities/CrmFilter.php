<?php

namespace Modules\Crm\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class CrmFilter extends TenancyModel
{
    protected $table = 'crm_filters';
    protected $fillable = [
        'id',
        'name',
        'filter_type_id',
        'created_by',
        'save_selected_fields',
        'is_default',
    ];

    /**
     * @return mixed
     */
    public function getFilterConditions()
    {
        return $this->hasMany('Modules\Crm\Entities\CrmFilterCondition', 'filter_id', 'id')->select([
            'component',
            'condition',
            'condition_type',
            'value',
            'field_default',
            'field_custom',
            'filter_type_id',
            'field_name',
            'is_default',
            'field_id',
        ]);
    }

    /**
     * @return mixed
     */
    public function getFilterConditionsWithRules()
    {
        return $this->hasMany('Modules\Crm\Entities\CrmFilterCondition', 'filter_id', 'id')->with();
    }

    /**
     * @return mixed
     */
    public function getFilterSelectedFields()
    {
        return $this->hasMany('Modules\Crm\Entities\CrmFilterField', 'filter_id', 'id')->with();
    }

    /**
     * @return mixed
     */
    public function getFilterType()
    {
        return $this->hasOne('Modules\Crm\Entities\CrmFilterType', 'id', 'filter_type_id')->select(
            [
                'name',
                'component',
                'identifier',
            ]
        );
    }

    /**
     * @return mixed
     */
    public function getFilterField()
    {
        return $this->belongsTo('Modules\Crm\Entities\CrmFilterField', 'id', 'filter_id')->select(
            [
                'value',
            ]
        );
    }

}