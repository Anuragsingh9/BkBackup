<?php

namespace App\Model;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

//use Illuminate\Database\Eloquent\Model;

class SkillTabs extends TenancyModel
{
    protected $connection= 'maria';
    protected $casts = [
        'visible' => 'array'
    ];
    protected $fillable = ['name', 'created_by', 'is_news_interested', 'is_locked', 'skill_tab_formet', 'is_valid', 'sort_order', 'added_to_presence', 'visible','tab_type'];

    

    public function skills()
    {
        return $this->hasMany('App\Model\Skill', 'skill_tab_id')->where('is_valid', 1)->orderByRaw('CAST(sort_order AS UNSIGNED) ASC');

    }

}
