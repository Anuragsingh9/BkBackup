<?php

namespace Modules\Resilience\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultationAnswer extends TenancyModel
{
    use SoftDeletes;

    protected $table = 'consultation_answers';

    protected $casts = [
        'answer' => 'array'
    ];

    protected $fillable = [
        'consultation_uuid',
        'user_id',
        'user_workshop_id',
        'consultation_question_id',
        'answer',
        'manual_answer'
    ];

    public function consultation() {
        return $this->belongsTo('Modules\Resilience\Entities\Consultation')->withoutGlobalScopes();
    }

    public function consultationQuestion() {
        return $this->belongsTo('Modules\Resilience\Entities\ConsultationQuestion')->withoutGlobalScopes();
    }

    public function user() {
        return $this->belongsTo('App\User')->withoutGlobalScopes();
    }
}
