<?php

namespace Modules\Cocktail\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Model;

class CallLog extends TenancyModel
{
    protected $table = 'event_q_call_logs';
    protected $fillable = ['user_call_id','user_conv_id'];
}
