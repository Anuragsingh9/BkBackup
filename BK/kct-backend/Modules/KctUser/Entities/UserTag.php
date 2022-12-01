<?php

namespace Modules\KctUser\Entities;

use Hyn\Tenancy\Abstracts\TenantModel;

class UserTag extends TenantModel {

    protected $table = 'kct_user_tags';
    protected $fillable = ['user_id', 'tag_id'];

}
