<?php

namespace Modules\Qualification\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class QualificationTemplate extends TenancyModel
{
    protected $fillable = ['title', 'language', 'file'];
    use SoftDeletes;

}
