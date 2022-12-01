<?php

namespace Modules\Resilience\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultationQuestionType extends TenancyModel
{
    use SoftDeletes;

    protected $table = 'consultation_question_types';

    protected $casts = [
        'format' => 'array'
    ];

    protected $fillable = [
        'question_type',
        'is_enable',
        'show_add_allow_button',
        'format'
    ];

    public function consultationQuestion() {
        return $this->hasMany('Modules\Resilience\Entities\ConsultationQuestion')->withoutGlobalScopes();
    }
}
