<?php

namespace App;

//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class Notification extends TenancyModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'to_id',
        'from_id',
        'title',
        'message',
        'json_message_data',
        'type',
        'read'
    ];
}
