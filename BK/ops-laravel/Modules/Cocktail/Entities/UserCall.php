<?php

namespace Modules\Cocktail\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Model;
use Modules\Cocktail\Entities\UserCallConvo;


class UserCall extends TenancyModel
{
    protected $table = 'event_q_user_calls';
    protected $fillable = ['from_id','to_id','status','event_uuid'];

    public function callConversation(){
        return $this->hasMany(UserCallConvo::class,'user_call_id','id');
    }
}
