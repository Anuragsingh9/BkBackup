<?php

namespace Modules\Newsletter\Entities;
use Carbon\Carbon;
use Auth;
// use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class NewsletterList extends TenancyModel
{
	protected $table = 'newsletter_lists';
    protected $fillable = ['list_id', 'newsletter_id'];

    public function Lists()
    {
        return $this->hasOne('App\Model\ListModel','id', 'list_id')->withCount('newsletter_contacts', 'users');
    }
     /**
     * Always format date
     */
    public function getUpdatedAtAttribute($value) {
        // $lang = session()->has('lang') ? session()->get('lang') : "FR";
        $lang = Auth::check() ? json_decode(Auth::user()->setting)->lang : "FR";
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

}
