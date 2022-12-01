<?php

namespace Modules\Qualification\Entities;

use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class ReferrerField extends TenancyModel
{
    //field_id=Fields table id fields
    protected $fillable = ['candidate_id','field_id','refreer_id','status','for_card_instance','step_id','used'
    ,'file'
    ,'uploaded_on'
    ];
    // protected $table = 'refreer_fields';
    public function candidate()
    {
        return $this->hasOne('App\User', 'id', 'candidate_id')/*->where(['sub_role'=>'C1'])->select('id','fname','lname','email')*/;
    }
    public function field()
    {
        return $this->hasOne('Modules\Qualification\Entities\Field', 'id', 'field_id')->with('skill');
    }
    public function referrer()
    {
        return $this->hasOne('Modules\Qualification\Entities\Referrer', 'id', 'refreer_id');
    }

    public function domain()
    {
        return $this->hasOne('Modules\Qualification\Entities\Field', 'id', 'field_id');
    }

    public function step()
    {
        return $this->hasOne('Modules\Qualification\Entities\Step', 'id', 'step_id');
    }
}
