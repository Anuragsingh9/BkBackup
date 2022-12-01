<?php

namespace Modules\Crm\Entities;
// use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Carbon\Carbon;

class CrmNote extends TenancyModel
{
    protected $fillable = [
        'notes',
        'created_by',
        'type',
    ];
    protected $with = ['createdBy'];
    /**
     * Get all of the owning crm_noteable models.
     */
    public function crm_noteable()
    {
        return $this->morphTo();
    }

    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by')->select(['id','fname','lname','avatar']);
    }
     /**
     * Always format date
     */
     public function getCreatedAtAttribute($value) {
        $lang = session()->has('lang') ? session()->get('lang') : "FR";
        if($lang=='FR'){
            setlocale(LC_TIME, 'fr_FR');
            $date=Carbon::parse($value)->format('d F Y');
            $date_fr = ['janvier','février','mars','avril','mai','juin','juillet','aout','septembre','octobre','novembre','décembre'];
            $dateEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November','December'];
            return str_replace($dateEn, $date_fr, $date);
        }
        else{
            return Carbon::parse($value)->format('d F Y');
        }
    }
}
