<?php

namespace Modules\KctAdmin\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Hyn\Tenancy\Abstracts\TenantModel;

/**
 * @property integer label_id
 * @property string value
 * @property string locale
 *
 * Class LabelCustomizationLocales
 * @package Modules\KctAdmin\Entities
 */
class LabelCustomizationLocales extends TenantModel {
    use HasFactory;

    protected $fillable = ['label_id', 'value', 'locale'];

}
