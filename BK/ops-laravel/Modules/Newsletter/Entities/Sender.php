<?php

namespace Modules\Newsletter\Entities;
use Carbon\Carbon;

// use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sender extends TenancyModel
{
    protected $table = 'newsletter_senders';

    protected $fillable = ['user_id','short_name','description','from_name','email','address','city','state','postal','country'];

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
    public function newsletters()
    {
        return $this->hasMany('Modules\Newsletter\Entities\Newsletter');
    }
    use SoftDeletes;
    /**
     * Always format date
     */
     public function getUpdatedAtAttribute($value) {
        $lang = session()->has('lang') ? session()->get('lang') : "FR";
        if($lang=='FR'){
            setlocale(LC_TIME, 'fr_FR');
            $date=Carbon::parse($value)->format('F d,Y');
            $date_fr = ['janvier','février','mars','avril','mai','juin','juillet','aout','septembre','octobre','novembre','décembre'];
            $dateEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November','December'];
            return str_replace($dateEn, $date_fr, $date);
        }
        else{
            return Carbon::parse($value)->format('F d,Y');
        }
    }
    public function getDescriptionAttribute($value) {
        if($value=='null'){
            $val='';
            return $val;
        }
        else{
         return $value;   
        }
    }
}
