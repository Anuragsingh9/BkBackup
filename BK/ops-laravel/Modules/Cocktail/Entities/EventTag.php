<?php

namespace Modules\Cocktail\Entities;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventTag extends TenancyModel
{
    protected $fillable = ['name','created_by'];
    public function user(){
        return $this->hasOne('App\User', 'id', 'user_id')->select(['id', 'fname', 'lname', 'email', 'mobile', 'avatar']);
    }
}
