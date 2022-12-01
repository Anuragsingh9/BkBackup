<?php

namespace App;

//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class Hostname extends TenancyModel {

    public function organisation() {

        return $this->belongsTo('App\Organisation', 'id', 'account_id');
    }

    public function website() {

        return $this->belongsTo('App\hostnames', 'website_id', 'id');
    }

}
