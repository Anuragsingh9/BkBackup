<?php
    
    namespace Modules\Newsletter\Entities;
    
    //    use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
    use Illuminate\Database\Eloquent\Model;
    
    class AdobePhotosAccountCredit extends Model
    {
        protected $table = 'adobe_photos_account_credit';
        protected $fillable = ['account_id', 'available_credit', 'monthly_allowed_credit'];
    }
