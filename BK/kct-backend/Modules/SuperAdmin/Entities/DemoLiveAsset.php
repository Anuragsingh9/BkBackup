<?php

namespace Modules\SuperAdmin\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DemoLiveAsset extends Model
{
    use HasFactory;

    protected $fillable = ['asset_path','asset_type','category'];

    protected static function newFactory()
    {
        return \Modules\SuperAdmin\Database\factories\DemoLiveAssestFactory::new();
    }
}
