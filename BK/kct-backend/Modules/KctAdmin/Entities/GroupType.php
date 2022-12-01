<?php

namespace Modules\KctAdmin\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GroupType extends TenantModel
{
    use HasFactory;

    protected $fillable = ['group_type'];

    protected static function newFactory()
    {
        return \Modules\KctAdmin\Database\factories\GroupTypeFactory::new();
    }
}
