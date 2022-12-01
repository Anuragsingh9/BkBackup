<?php

namespace Modules\Newsletter\Entities;
use Carbon\Carbon;
use Auth;
// use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class ScheduleTime extends TenancyModel
{
    protected $table = 'newsletter_schedule_timings';
    protected $fillable = ['sender_id','newsletter_id','schedule_time'];

    public function sender()
    {
        return $this->hasOne('Modules\Newsletter\Entities\Sender','id','sender_id');
    }

    public function newsletter()
    {
        return $this->hasOne('Modules\Newsletter\Entities\Newsletter','id','newsletter_id');
    }
    /**
     * Always format date
     */
    public function getScheduleTimeAttribute($value) {
        // $lang = session()->has('lang') ? session()->get('lang') : "FR";
        $lang = Auth::check() ? json_decode(Auth::user()->setting)->lang : "FR";
        if($lang=='FR'){
            setlocale(LC_TIME, 'fr_FR');
            $date=Carbon::parse($value)->format('F d, Y H:i');
            $date_fr = ['janvier','février','mars','avril','mai','juin','juillet','aout','septembre','octobre','novembre','décembre'];
            $dateEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November','December'];
            return str_replace($dateEn, $date_fr, $date);
        }
        else{
            return Carbon::parse($value)->format('F d, Y H:i');
        }
    }
}
