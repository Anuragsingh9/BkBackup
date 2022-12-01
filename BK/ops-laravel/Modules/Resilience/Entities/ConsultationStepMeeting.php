<?php
    
    namespace Modules\Resilience\Entities;
    
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
    
    class ConsultationStepMeeting extends TenancyModel
    {
        use SoftDeletes;
        
        protected $table = 'consultation_step_meetings';
        
        protected $fillable = [
            'meeting_id',
            'consultation_uuid',
            'consultation_step_id',
        ];
        
        public function consultation()
        {
            return $this->belongsTo('Modules\Resilience\Entities\Consultation')->withoutGlobalScopes();
        }
        
        public function consultationStep()
        {
            return $this->belongsTo('Modules\Resilience\Entities\ConsultationStep')->withoutGlobalScopes()->orderBy('step_type', 'ASC');
        }
        
        public function meeting()
        {
            return $this->belongsTo('App\Meeting')->withoutGlobalScopes()->select(['id','name','workshop_id','start_time','end_time','date']);
        }
    }
