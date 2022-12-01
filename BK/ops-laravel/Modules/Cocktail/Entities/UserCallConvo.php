<?php

namespace Modules\Cocktail\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Model;

class UserCallConvo extends TenancyModel
{
    protected $table = 'event_q_user_call_convos';
    protected $fillable = ['user_call_id','conversation_uuid','space_uuid','event_uuid'];
}
