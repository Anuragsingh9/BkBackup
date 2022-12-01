<?php

namespace Modules\KctAdmin\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Hyn\Tenancy\Abstracts\TenantModel;

class LabelCustomizations extends TenantModel {
    use HasFactory;

    protected $fillable = [];
}
