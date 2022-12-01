<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
//use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class OpsModule extends Model
{
    public $fillable = ['label_en', 'label_fr', 'tooltip_en','tooltip_fr','sort_order'];
    protected $table = 'ops_modules';
    
}
