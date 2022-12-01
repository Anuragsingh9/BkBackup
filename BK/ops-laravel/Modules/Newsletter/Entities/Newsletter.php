<?php

namespace Modules\Newsletter\Entities;
use Carbon\Carbon;
use Auth;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class Newsletter extends TenancyModel
{
    protected $table = 'newsletters';
    protected $fillable = ['name', 'short_name', 'description', 'url', 'sender_id', 'template_id', 'html_code','subject'];

    public function sender()
    {
        return $this->hasOne('Modules\Newsletter\Entities\Sender', 'id', 'sender_id');
    }

    public function template()
    {
        return $this->hasOne('Modules\Newsletter\Entities\Template', 'id', 'template_id');
    }

    public function scheduleTime()
    {
        return $this->belongsTo('Modules\Newsletter\Entities\ScheduleTime','id','newsletter_id');
    }

    public function blocks()
    {
        return $this->hasMany('Modules\Newsletter\Entities\NewsletterBlock', 'newsletter_id');
    }
/**
     * Always format date
     */
    public function getUpdatedAtAttribute($value) {
        $lang = Auth::check() ? json_decode(Auth::user()->setting)->lang : "FR";
        if($lang=='FR'){
            setlocale(LC_TIME, 'fr_FR');
            $date=Carbon::parse($value)->format('F d, Y');
            $date_fr = ['janvier','février','mars','avril','mai','juin','juillet','aout','septembre','octobre','novembre','décembre'];
            $dateEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November','December'];
            return str_replace($dateEn, $date_fr, $date);
        }
        else{
            return Carbon::parse($value)->format('F d, Y');
        }
    }
}
