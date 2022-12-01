<?php

namespace Modules\Newsletter\Entities;

// use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class IContact extends TenancyModel
{
    protected $table = 'newsletter_icontact_metas';
    protected $fillable = ['column_id','icontact_id','type'];

}
