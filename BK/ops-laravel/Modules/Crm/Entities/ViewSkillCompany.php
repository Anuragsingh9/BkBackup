<?php
    
    namespace Modules\Crm\Entities;
    
    use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
    
    class ViewSkillCompany extends TenancyModel
    {
        protected $table = 'skill_company_view';
        
        public function user()
        {
            return $this->belongsToMany('App\User', 'entity_users', 'entity_id', '', 'entity_id');
        }
        
        public function industry()
        {
            return $this->hasOne('App\Industry', 'id', 'industry_id');
        }
    
        public function contact()
        {
            return $this->belongsToMany('App\Model\Contact', 'entity_users', 'entity_id', '', 'entity_id');
        }
    }
