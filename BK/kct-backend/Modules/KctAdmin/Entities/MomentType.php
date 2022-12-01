<?php

namespace Modules\KctAdmin\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MomentType extends Model
{
    use HasFactory;

    protected  $table = 'event_moment_types';
    protected $fillable = ['name'];

//    protected static function newFactory()
//    {
//        return \Modules\KctAdmin\Database\factories\MomentTypeFactory::new();
//    }
}
