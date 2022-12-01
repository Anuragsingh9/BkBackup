<?php

namespace Modules\Crm\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class ViewSkillUser extends TenancyModel
{
    protected $table = 'skill_user_view';

    public function userSkillV() {
        return $this->hasOne('App\Model\UserSkill', 'id', 'user_skill_id');
    }
    public function industry()
    {
        return $this->hasOne('App\Industry', 'id', 'industry_id');
    }
    
    public function entity()
    {
        return $this->belongsToMany('App\Entity', 'entity_users','user_id','entity_id','user_id')->with('entityLabel:id,entity_id,entity_label');
    }
    public function entityUser()
    {
        return $this->hasMany('App\EntityUser', 'user_id', 'id')->with(['entity']);
    }
   
}
