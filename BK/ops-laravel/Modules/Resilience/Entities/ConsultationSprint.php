<?php

namespace Modules\Resilience\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class ConsultationSprint extends TenancyModel
{
    use SoftDeletes;

    protected $table = 'consultation_sprints';

    protected $fillable = [
        'title',
        'description_1',
        'description_2',
        'description_3',
        'image_non_selected',
        'image_selected',
        'consultation_uuid',
        'sort_order',
        'is_accessible'
    ];

    public function consultation() {
        return $this->belongsTo('Modules\Resilience\Entities\Consultation')->withoutGlobalScopes()->whereNull('deleted_at');
    }

    public function consultationStepByStepType()
    {
        return $this->hasMany('Modules\Resilience\Entities\ConsultationStep')->orderByRaw('CAST(sort_order AS UNSIGNED) ASC')/*->orderBy('step_type','ASC')*/->whereNull('deleted_at');
    }

    public function consultationStep()
    {
        return $this->hasMany('Modules\Resilience\Entities\ConsultationStep')->orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->whereNull('deleted_at');
    }
}
