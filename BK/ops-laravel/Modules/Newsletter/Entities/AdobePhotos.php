<?php

namespace Modules\Newsletter\Entities;

//use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Model;
class AdobePhotos extends Model {
    protected $table = 'adobe_photos';

    protected $fillable = ['adobe_photo_id', 'bought_at', 'search_tag'];
}
