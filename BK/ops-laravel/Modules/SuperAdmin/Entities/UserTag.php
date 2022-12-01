<?php

namespace Modules\SuperAdmin\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTag extends Model {
    use SoftDeletes;
    
    protected $table = 'event_pp_users_tags';
    protected $fillable = ['user_id', 'tag_EN', 'tag_FR', 'tag_type', 'status'];
}
