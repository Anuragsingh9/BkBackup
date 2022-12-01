<?php
    
    namespace Modules\Qualification\Entities;
    
    use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
    
    class DomainCheckbox extends TenancyModel
    {
        protected $fillable = ['step_id', 'skill_id'];
        protected $table = 'qualification_domain_checkboxes';
        
        public function domainSkill()
        {
            return $this->hasOne('App\Model\Skill', 'id','skill_id');
        }
        
        public function domainStep()
        {
            return $this->hasOne('Modules\Qualification\Entities\Step', 'id','step_id');
        }
    }
