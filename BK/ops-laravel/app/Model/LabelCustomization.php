<?php

namespace App\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
//use Illuminate\Database\Eloquent\Model;

class LabelCustomization extends TenancyModel
{
    public $fillable = array('name','on_off','default_en','default_fr','custom_en','custom_fr');
}
