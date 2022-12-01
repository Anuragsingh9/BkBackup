<?php

namespace Modules\SuperAdmin\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SceneryLocale extends Model
{
    use HasFactory;

    protected $table = 'scenery_category_locales';
    protected $fillable = [
        'category_id',
        'value',
        'locale',
    ];

    protected static function newFactory()
    {
        return \Modules\SuperAdmin\Database\factories\SceneryLocaleFactory::new();
    }
}

