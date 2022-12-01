<?php

namespace App;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Modules\Cocktail\Entities\EventDummyUser;

class DummyUsers extends TenancyModel {
    
    protected $fillable = [
        'fname',
        'lname',
        'avatar',
        'company',
        'company_position',
        'union',
        'union_position',
        'video_url',
        'type',
    ];
    
    public function eventDummyUser() {
        return $this->hasOne(EventDummyUser::class, 'dummy_user_id', 'id');
    }
}
