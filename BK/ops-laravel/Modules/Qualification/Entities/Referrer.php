<?php
    
    namespace Modules\Qualification\Entities;
    
    use Illuminate\Database\Eloquent\Model;
    use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
    
    class Referrer extends TenancyModel
    {
        protected $fillable = ['fname', 'lname', 'email', 'company', 'position', 'referrer_type', 'pdf_upload', 'phone', 'address', 'mobile', 'zip_code'];
        protected $table = 'qualification_referrers';
        
        public function userReffrerReminder()
        {
            return $this->hasone('Modules\Qualification\Entities\QualificationUserReminder', 'referrer_id', 'id')->where('type_of_email', 6);
        }
    }
