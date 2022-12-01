<?php

namespace Modules\KctAdmin\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FavouriteGroup extends TenantModel
{
    use HasFactory;

    protected $table = "user_fav_groups";
    protected $fillable = ['user_id','group_id'];

    protected static function newFactory()
    {
        return \Modules\KctAdmin\Database\factories\FavouriteGroupFactory::new();
    }
}
