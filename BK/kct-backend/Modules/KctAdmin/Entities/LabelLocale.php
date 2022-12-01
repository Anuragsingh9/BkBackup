<?php

namespace Modules\KctAdmin\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LabelLocale extends TenantModel {
    protected $fillable = ['label_id', 'value', 'locale', 'group_id'];

    public function label() {
        return $this->belongsTo(Label::class, 'label_id', 'id');
    }
}
