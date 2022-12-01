<?php

namespace Modules\Qualification\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class QualificationUserReminder extends TenancyModel
{
    //1 for FirstRenewal
    //2 For Reminder Of First Renewal
    //3 For Reminder Of Fourth Renewal
    //4 for FourthRenewal
    //5 for RegistrationReminder
    //6 for ReffrerReminder

    protected $fillable = ['user_id','reminder_for','type_of_email','referrer_id'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
