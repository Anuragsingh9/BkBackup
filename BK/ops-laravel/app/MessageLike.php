<?php

namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class MessageLike extends TenancyModel
{
	public $fillable=['message_id','workshop_id', 'message_reply_id', 'user_id', 'status'];
	
}
