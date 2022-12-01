<?php

namespace Modules\Newsletter\Entities;

// use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class Icontact_meta extends TenancyModel
{
    protected $fillable = [
        'column_id','icontact_id','type'
    ];
    protected $table = 'newsletter_icontact_metas';
    
    public function newsletter_contacts()
    {
        return $this->belongsTo('Modules\Newsletter\Entities\Contact','column_id');
    }
    public function users()
    {
        return $this->belongsTo('App\User', 'column_id');
    }
}
