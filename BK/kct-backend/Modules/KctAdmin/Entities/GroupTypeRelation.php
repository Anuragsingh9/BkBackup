<?php

namespace Modules\KctAdmin\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GroupTypeRelation extends TenantModel
{
    use HasFactory;

    protected $fillable = ['group_id','type_id'];

    protected static function newFactory()
    {
        return \Modules\KctAdmin\Database\factories\GroupTypeRelationFactory::new();
    }
}
