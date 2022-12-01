<?php
    
    namespace Modules\Newsletter\Entities;
    
    //    use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
    use Illuminate\Database\Eloquent\Model;
    
    class AdobePhotosTracking extends Model
    {
        protected $table = 'adobe_photos_tracking';
        
        protected $fillable = ['adobe_photo_id', 'account_id', 'user_id', 'type'];
        
        public function hostname()
        {
            return $this->hasOne('Hyn\Tenancy\Models\Hostname', 'id', 'account_id');
        }
    }
