<?php

namespace Modules\Qualification\Entities;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vote extends TenancyModel
{
    protected $fillable = ['type_of_votes','vote_name','vote_short_name','vote_description','is_sync'];
use SoftDeletes;
}


