<?php

namespace Modules\Crm\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class UserSocialAccountLink extends TenancyModel {

    protected $table = 'user_social_account_links';

    protected $fillable = [
        'user_id',
        'contact_id',
        'channel',
        'url',
        'is_main',
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function user() {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
