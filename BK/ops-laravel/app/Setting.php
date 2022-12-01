<?php
    
    namespace App;
    //use Illuminate\Database\Eloquent\Model;
    use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
    
    class Setting extends TenancyModel
    {
        protected $connection= 'maria';
        public $fillable = ['id', 'setting_key', 'setting_value'];
    }
