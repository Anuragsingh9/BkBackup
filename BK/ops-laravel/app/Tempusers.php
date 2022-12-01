<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class Tempusers extends TenancyModel
{
	public $table = 'temp_users';
    protected $fillable = [
        'name', 'email', 'password','fname','lname','role','hash_code','login_code','fcm_token'
    ];
}
