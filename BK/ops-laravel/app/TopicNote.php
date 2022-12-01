<?php

namespace App;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class TopicNote extends TenancyModel
{
	protected $fillable=['topic_id','meeting_id','user_id','topic_note'];
}
