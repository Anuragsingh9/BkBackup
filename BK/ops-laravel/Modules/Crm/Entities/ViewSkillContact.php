<?php

namespace Modules\Crm\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class ViewSkillContact extends TenancyModel
{
    protected $table = 'skill_contact_view';
    public function industry()
    {
        return $this->hasOne('App\Industry', 'id', 'industry_id');
    }
    
    public function entity()
    {
        return $this->belongsToMany('App\Entity', 'entity_users', 'contact_id', 'entity_id','user_id');
    }
    public function entityUser()
    {
        return $this->hasMany('App\EntityUser', 'contact_id', 'id')->with(['entity']);
    }
    
    
}
