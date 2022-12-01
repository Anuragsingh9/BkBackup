<?php

namespace Modules\Resilience\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultationQuestion extends TenancyModel
{
    use SoftDeletes;

    protected $table = 'consultation_questions';

    protected $casts = [
        'options' => 'array'
    ];

    protected $fillable = [
        'consultation_step_id',
        'consultation_question_type_id',
        'question',
        'description',
        'is_mandatory',
        'allow_add_other_answers',
        'options',
        'comment',
        'sort_order',
        'displayFriendRequest',
        'order'
    ];

    public function consultationStep() {
        return $this->belongsTo('Modules\Resilience\Entities\ConsultationStep')->withoutGlobalScopes();
    }

    public function consultationQuestionType() {
        return $this->belongsTo('Modules\Resilience\Entities\ConsultationQuestionType')->withoutGlobalScopes();
    }

    public function consultationAnswer() {
        return $this->hasMany('Modules\Resilience\Entities\ConsultationAnswer')->withoutGlobalScopes();
    }
}
