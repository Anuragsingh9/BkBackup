<?php

namespace Modules\KctUser\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * * @method static SystemVirtualBackgrounds create(array $array)
 */
class SystemVirtualBackgrounds extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_url',
        'bg_type',
    ];

}
