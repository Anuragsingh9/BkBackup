<?php

    namespace Modules\Resilience\Entities;

    use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
    use Illuminate\Database\Eloquent\SoftDeletes;

    class ConsultationStep extends TenancyModel
    {
        use SoftDeletes;
        protected $casts = [
            'extra_fields' => 'array',
        ];

        protected $table = 'consultation_steps';

        protected $fillable = [
            'title',
            'description',
            'image',
            'step_type',
            'active',
            'answerable',
            'display_results_until',
            'is_redirection',
            'redirect_url',
            'redirect_url_label',
            'extra_fields',
            'title_text',
            'sort_order',
        ];

        public function consultationSprint()
        {
            return $this->belongsTo('Modules\Resilience\Entities\ConsultationSprint')->orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->whereNull('deleted_at');
        }

        public function stepMeetings()
        {
            return $this->hasOne('Modules\Resilience\Entities\ConsultationStepMeeting')->withoutGlobalScopes()->whereNull('deleted_at');
        }

        public function consultationQuestion()
        {
            return $this->hasMany('Modules\Resilience\Entities\ConsultationQuestion')->orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->whereNull('deleted_at');
        }
    }
