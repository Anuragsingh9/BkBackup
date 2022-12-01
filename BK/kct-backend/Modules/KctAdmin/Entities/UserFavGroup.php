<?php

namespace Modules\KctAdmin\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserFavGroup extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'group_id'];

    protected static function newFactory()
    {
        return \Modules\KctAdmin\Database\factories\UserFavGroupFactory::new();
    }
}
