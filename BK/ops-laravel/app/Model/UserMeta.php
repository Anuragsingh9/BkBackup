<?php

namespace App\Model;

//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class UserMeta extends TenancyModel
{
    //is_final_save 0->not submited,1->submitedByCandidate,2->ForwardToExpert
    public $fillable = ['current_step_id', 'setting','user_id','is_final_save','saved_at','final_by'];
    protected $table = 'user_metas';
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
}
