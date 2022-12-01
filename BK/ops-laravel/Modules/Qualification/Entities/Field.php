<?php

namespace Modules\Qualification\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class Field extends TenancyModel
{
    protected $table = 'qualification_fields';

    protected $fillable = ['step_id', 'field_id', 'sort_order'];

    use SoftDeletes;

//relation with Skill Model
    public function skill()
    {
        return $this->hasOne('App\Model\Skill', 'id', 'field_id');
    }

    public function step()
    {
        return $this->hasOne('Modules\Qualification\Entities\Step', 'id','step_id');
    }
}
