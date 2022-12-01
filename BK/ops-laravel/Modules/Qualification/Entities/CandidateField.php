<?php

namespace Modules\Qualification\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class CandidateField extends TenancyModel
{
    protected $table = 'candidate_fields';
    protected $fillable = ['qualification_field_id','user_id'];

}
