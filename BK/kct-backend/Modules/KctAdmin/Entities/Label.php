<?php

namespace Modules\KctAdmin\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Label extends TenantModel {
    protected $fillable = ['name'];

    public function locales(): HasMany {
        return $this->hasMany(LabelLocale::class, 'label_id', 'id');
    }

}
