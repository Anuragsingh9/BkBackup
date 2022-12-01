<?php
namespace App;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

// use Illuminate\Database\Eloquent\Model;

class PersonalMessageReply extends TenancyModel
{
	protected $table='personal_message_replies';
    public function user(){
        return $this->hasOne('App\User', 'id', 'from_user_id');
    }
}
