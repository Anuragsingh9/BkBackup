<?php

namespace Modules\Cocktail\Entities;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventTagMata extends TenancyModel
{
    protected $fillable = ['user_id','tag_id'];
    protected $table="event_tag_metas";
    public function tag()
    {
        return $this->hasOne(EventTag::class, 'id', 'tag_id');
    }
}
