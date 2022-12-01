<?php

namespace App;
//use Illuminate\Database\Eloquent\Model;
use App\Scopes\MeetingScope;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class Meeting extends TenancyModel
{
    
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        
        static::addGlobalScope(new MeetingScope);
    }
    
    
	public $fillable=['id', 'name', 'code', 'description', 'place', 'mail', 'contact_no', 'image', 'header_image', 'lat', 'long', 'date', 'start_time', 'end_time', 'meeting_date_type', 'meeting_type', 'workshop_id', 'user_id', 'visibility', 'status', 'prepd_published_on', 'repd_published_on', 'prepd_published_by_user_id', 'repd_published_by_user_id', 'validated_prepd', 'validated_repd', 'redacteur', 'is_offline', 'is_downloaded','is_import','is_repd_final','is_prepd_final','meeting_meal_type'];

	function doodleDates(){
		return $this->hasMany('App\DoodleDates','meeting_id','id');
	}
	function workshop(){
		return $this->hasOne('App\Workshop','id','workshop_id');
	}
	function presences(){
		return $this->hasOne('App\Presence','meeting_id','id');
	}
	function topics(){
		return $this->hasMany('App\Topic','meeting_id','id')->with('docs','notes');
	}
    function consultation()
    {
        return $this->belongsToMany('Modules\Resilience\Entities\Consultation', 'consultation_step_meetings');
    }
}
