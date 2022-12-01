<?php

namespace Modules\SuperAdmin\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SceneryAsset extends Model {
    use HasFactory;

    public static $assetType_image = 1;
    public static $assetType_color = 2;

    protected $table = 'scenery_category_assets';
    protected $fillable = [
        'category_id',
        'asset_path',
        'asset_type', // 1 image, 2 color
        'asset_settings',
    ];

    protected $casts = ['asset_settings' => 'array'];

    protected static function newFactory() {
        return \Modules\SuperAdmin\Database\factories\SceneryAssetFactory::new();
    }
}
