<?php

namespace Modules\KctAdmin\Entities;

use Illuminate\Database\Eloquent\Model;

class Security extends Model {
    protected $table = 'security_groups';
    protected $fillable = ['name'];

}
