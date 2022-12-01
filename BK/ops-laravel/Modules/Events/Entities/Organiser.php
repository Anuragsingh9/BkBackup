<?php

namespace Modules\Events\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organiser extends TenancyModel {
    use SoftDeletes;

    protected $table = 'event_organisers';
    protected $fillable = ['fname', 'lname', 'company', 'image', 'email', 'phone', 'website'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function events() {
        return $this->morphToMany('Modules\Events\Entities\Event', 'eventable');
    }
}
