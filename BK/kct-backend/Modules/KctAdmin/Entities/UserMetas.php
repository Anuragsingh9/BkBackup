<?php

namespace Modules\KctAdmin\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserMetas extends TenantModel
{
    use HasFactory;

    protected $fillable = ['user_id', 'signup_type', 'added_by'];

    protected static function newFactory()
    {
        return \Modules\KctAdmin\Database\factories\UserMetasFactory::new();
    }
}
