<?php

namespace Modules\Resilience\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UseUuid;

class Consultation extends TenancyModel
{
    use SoftDeletes, UseUuid;

    protected $table = 'consultations';
    protected $primaryKey = 'uuid';
    protected $fillable = [
        'user_id',
        'workshop_id',
        'name',
        'internal_name',
        'start_date',
        'end_date',
        'display_results_until',
        'has_welcome_step',
        'is_reinvent',
        'public_reinvent',
        'long_name',
        'allow_to_go_back'
    ];

//    public function workshop() {
//        return $this->hasMany('App\Workshop', 'id', 'workshop_id')->withoutGlobalScopes();
//    }
//
//    public function user() {
//        return $this->hasMany('App\User', 'id', 'user_id')->withoutGlobalScopes();
//    }

    public function consultationSprint() {
        return $this->hasMany('Modules\Resilience\Entities\ConsultationSprint')->orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->whereNull('deleted_at');
    }

    public function consultationAnswer() {
        return $this->hasMany('Modules\Resilience\Entities\ConsultationAnswer')->withoutGlobalScopes()->whereNull('deleted_at');
    }

    public function consultationAnsweredUser() {
        return $this->hasMany('Modules\Resilience\Entities\ConsultationAnswerUser')->where(['answered' => 1])->withoutGlobalScopes()->whereNull('deleted_at');
    }

    public function consultationAnswerUser() {
        return $this->hasMany('Modules\Resilience\Entities\ConsultationAnswerUser')->withoutGlobalScopes()->whereNull('deleted_at');
    }

    public function workshop() {
        return $this->hasOne('App\Workshop', 'id', 'workshop_id')->withoutGlobalScopes();
    }

    public function stepMeetings() {
        return $this->hasMany('Modules\Resilience\Entities\ConsultationStepMeeting')->withoutGlobalScopes()->whereNull('deleted_at');
    }
}
