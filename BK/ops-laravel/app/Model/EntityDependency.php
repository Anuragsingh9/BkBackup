<?php

namespace App\Model;

//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class EntityDependency extends TenancyModel
{
    public $fillable = ['entity_id', 'parent_id'];
    protected $table = 'entity_dependencies';

    public function entity()
    {
        return $this->belongsTo('App\Entity', 'parent_id', 'id')->select(['id', 'long_name','entity_type_id']);
    }
}
