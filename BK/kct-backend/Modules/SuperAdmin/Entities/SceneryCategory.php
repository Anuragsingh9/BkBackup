<?php

namespace Modules\SuperAdmin\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SceneryCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected static function newFactory()
    {
        return \Modules\SuperAdmin\Database\factories\SceneryCategoryFactory::new();
    }

    public function asset(){
        return $this->hasMany(SceneryAsset::class,'category_id','id');
    }

    public function sceneryLocale(){
        return $this->hasMany(SceneryLocale::class,'category_id','id');
    }
}
