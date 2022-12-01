<?php

namespace App\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

//use Illuminate\Database\Eloquent\Model;

class SkillTabFormat extends TenancyModel
{
    public $fillable = ['name_en','name_fr','short_name','field_type'];
}
