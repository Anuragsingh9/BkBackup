<?php

namespace Modules\Crm\Entities;

//use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class Assistance extends TenancyModel
{
    protected $table = "crm_assistance_type";

    protected $fillable = ['assistance_type_name', 'assistance_type_short_name'];

    use SoftDeletes;

    /**
     * Always format date
     */
    // public function getCreatedAtAttribute($value)
    // {
    //     $lang = 'EN';
    //     if ($lang == 'FR') {
    //         setlocale(LC_TIME, 'fr_FR');
    //         $date = Carbon::parse($value)->format('d F Y');
    //         $date_fr = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'aout', 'septembre', 'octobre', 'novembre', 'décembre'];
    //         $dateEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    //         return str_replace($dateEn, $date_fr, $date);
    //     } else {
    //         return Carbon::parse($value)->format('d F Y');
    //     }
    // };
}
