<?php
namespace App;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
// use Illuminate\Database\Eloquent\Model;

class PersonalMessage extends TenancyModel
{
    public function fromUser()
    {
        return $this->hasOne('App\User', 'id', 'from_user_id');
    }
    public function toUser()
    {
        return $this->hasOne('App\User', 'id', 'to_user_id');
    }
    public function message_reply()
    {
        return $this->hasMany('App\PersonalMessageReply')->orderBy('created_at','desc');
    }
}
