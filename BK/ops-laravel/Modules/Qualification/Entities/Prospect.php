<?php

namespace Modules\Qualification\Entities;

// use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class Prospect extends TenancyModel
{
    protected $fillable = ['fname','lname','tel','email','company','reg_no','comment','zip_code','case','workshop_code','mobile'];
    
    public function workshop()
    {
        return $this->hasOne('App\Workshop', 'code1', 'workshop_code')->withoutGlobalScopes();
    }
}
