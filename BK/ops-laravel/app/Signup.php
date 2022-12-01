<?php

namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class Signup extends TenancyModel
{
    public $table = 'signup_temp';

}
