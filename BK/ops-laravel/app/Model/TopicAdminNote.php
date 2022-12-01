<?php

namespace App\Model;

// use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class TopicAdminNote extends TenancyModel
{
    protected $table = 'topic_admin_notes';
    protected $fillable = ['topic_note','user_id','topic_id','meeting_id','workshop_id','notes_updated_at','is_archived'];
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
}
