<?php

namespace App\Model;

//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class ActivityStatus extends TenancyModel
{
    public $fillable = ['en_label', 'fr_label', 'status'];
    protected $table = 'activity_statuses';

}
