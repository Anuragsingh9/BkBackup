<?php

namespace App\Model;

// use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListModel extends TenancyModel
{
    protected $fillable = [
        'name', 'description', 'type', 'typology_id','creation_type'
    ];
    protected $table = 'lists';

    use SoftDeletes;

    public function newsletter_contacts()
    {
        return $this->morphedByMany('Modules\Newsletter\Entities\Contact','listablesls');
    }
    public function users()
    {
        return $this->morphedByMany('App\User', 'listablesls');
    }
    public function icontact_meta()
    {
        return $this->belongsTo('Modules\Newsletter\Entities\Icontact_meta','id', 'column_id');
    }

}
