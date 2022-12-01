<?php

namespace App\Model;

// use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Carbon\Carbon;
class TaskComment extends TenancyModel
{
    protected $table='comments';
    protected $with='createdBy';
    public $fillable = ['task_id', 'user_id','comment','workshop_id'];
    public function user(){
        return $this->hasOne('App\User','id','user_id')->select(['id','fname','lname','avatar']);
    }

    /**
     * Get all of the owning commentable models.
     */
    public function commentable()
    {
        return $this->morphTo();
    }
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'user_id')->select(['id','fname','lname','avatar']);
    }

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
