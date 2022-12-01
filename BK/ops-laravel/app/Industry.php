<?php
    
    namespace App;
    //use Illuminate\Database\Eloquent\Model;
    use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
    
    class Industry extends TenancyModel
    {
        public $fillable = ['id', 'name', 'parent'];
        
        public function parent()
        {
            return $this->hasOne('App\Industry', 'id', 'parent');
        }
    }
