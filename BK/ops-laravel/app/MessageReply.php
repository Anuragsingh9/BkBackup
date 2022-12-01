<?php

namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class MessageReply extends TenancyModel
{
	public $fillable = array('id','message_id','reply_text','user_id');
	public $with=['user'];
	public function user(){
        return $this->hasOne('App\User','id','user_id')->select(['id','fname','lname','avatar','email','phone']);
    }
}
