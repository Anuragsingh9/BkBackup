<?php

namespace Modules\Cocktail\Entities;

use App\User;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Model;
use Modules\SuperAdmin\Entities\UserTag;

class EventUserTagRelation extends TenancyModel {
    protected $table = 'event_pp_tag_user_r';
    
    protected $fillable = ['user_id', 'tag_id'];
    
    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    
    public function tag() {
        return $this->setConnection('mysql')->belongsTo(UserTag::class, 'tag_id', 'id');
    }
}


