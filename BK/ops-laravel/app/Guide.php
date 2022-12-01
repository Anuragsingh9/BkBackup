<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
//use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class Guide extends Model
{
    protected $fillable = [
        'title_en',
        'title_fr',
        'upload_en',
        'upload_fr',
        'role',
    ];
}
