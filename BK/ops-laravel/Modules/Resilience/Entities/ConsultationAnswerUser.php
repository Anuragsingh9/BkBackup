<?php

namespace Modules\Resilience\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultationAnswerUser extends TenancyModel
{
    use SoftDeletes;

    protected $table = 'consultation_answer_user';

    protected $casts = [
        'answer_meta_data' => 'array'
    ];

    protected $fillable = [
        'consultation_uuid',
        'user_id',
        'answered',
        'answer_meta_data',
    ];

    public function consultation() {
        return $this->belongsTo('Modules\Resilience\Entities\Consultation')->withoutGlobalScopes();
    }


    public function user() {
        return $this->belongsTo('App\User')->withoutGlobalScopes();
    }
}
