<?php

namespace Modules\Qualification\Entities;

use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class UserDomain extends TenancyModel
{
    protected $fillable = ['user_id','domain'];
}
