<?php

namespace Modules\KctUser\Entities;

use Illuminate\Database\Eloquent\Model;

class UserClmVisibility extends Model {

    protected $fillable = ['user_id', 'fields'];

    protected $casts = [
        'fields' => 'array',
    ];


}
