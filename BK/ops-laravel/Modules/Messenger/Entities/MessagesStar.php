<?php

namespace Modules\Messenger\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class MessagesStar extends TenancyModel {
    protected $fillable = ['message_id', 'user_id'];
}
