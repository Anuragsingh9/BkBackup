<?php

namespace App;

//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class EntityUser extends TenancyModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'user_id',
        'type',
        'entity_id',
        'created_by',
        'entity_label',
        'contact_id',
        'membership_type',
        'consultation_sign_up_class_positions_id',
    ];

    public function entity()
    {
        return $this->belongsTo('App\Entity', 'entity_id', 'id')->select(['id', 'long_name','entity_type_id']);
    }

    public function contact()
    {
        return $this->belongsTo('App\Model\Contact', 'contact_id')->select(["id", "fname", "lname"]);
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id')->select(["id", "fname", "lname"]);
    }
}
