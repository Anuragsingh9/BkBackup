<?php

namespace Modules\Crm\Entities;

use Carbon\Carbon;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\SoftDeletes;


class AssistanceReport extends TenancyModel
{
    use SoftDeletes;
    protected $fillable = [
        'reports',
        'created_by',
        'crm_assistance_type_id',
        'type',
    ];
    protected $with = ['createdBy','assistanceType'];
    /**
     * Get all of the owning crm_noteable models.
     */
    public function assistance_reportable()
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
    
    public function assistanceType()
    {
        return $this->belongsTo('Modules\Crm\Entities\Assistance', 'crm_assistance_type_id')->select(['id','assistance_type_name','assistance_type_short_name']);
    }
}
