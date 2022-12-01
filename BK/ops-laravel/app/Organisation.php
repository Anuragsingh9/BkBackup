<?php

namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class Organisation extends TenancyModel
{

    public $table = 'organisation';
    public $fillable = array('fname','lname','email','password','name_org','members_count','acronym','sector','permanent_member','logo','icon','bashlinelogo');

}
