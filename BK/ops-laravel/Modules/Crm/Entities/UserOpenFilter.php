<?php
    
    namespace Modules\Crm\Entities;
    
    //    use Illuminate\Database\Eloquent\Model;
    use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
    
    class UserOpenFilter extends TenancyModel
    {
        protected $fillable = [
            'user_id',
            'filter_type_id',
            'filter_id',
        ];
        protected $table = 'crm_user_open_filters';
    }
