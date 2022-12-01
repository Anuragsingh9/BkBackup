<?php

namespace Modules\Newsletter\Entities;

use Carbon\Carbon;
 use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;
class Contact extends TenancyModel
{
    protected $fillable = [
        'fname', 'lname', 'email', 'phone','mobile','address','postal','city','country','status'
    ];
    protected $table = 'newsletter_contacts';

    use SoftDeletes;
    protected $hiddenInApi = [
        'status',
    ];
    public function lists()
    {
        return $this->morphToMany('App\Model\ListModel', 'listablesls');
    }

    /**
     * Always format date
     */
    public function getCreatedAtAttribute($value)
    {
        // $lang = session()->has('lang') ? session()->get('lang') : "FR";
         $lang = Auth::check() ? json_decode(Auth::user()->setting)->lang : "FR";
        if ($lang == 'FR') {
            setlocale(LC_TIME, 'fr_FR');
            $date = Carbon::parse($value)->format('F d, Y');
            $date_fr = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'aout', 'septembre', 'octobre', 'novembre', 'décembre'];
            $dateEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            return str_replace($dateEn, $date_fr, $date);
        } else {
            return Carbon::parse($value)->format('F d, Y');
        }
    }

    public function getFillable()
    {
        return $this->fillable;
    }


}
