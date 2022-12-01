<?php

namespace Modules\Newsletter\Entities;

// use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class ContactStatus extends TenancyModel
{
    protected $fillable = ['icontact_id','status'];
}
