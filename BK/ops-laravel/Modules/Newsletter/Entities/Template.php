<?php

namespace Modules\Newsletter\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
// use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

/**
 * Class Template
 * @package Modules\Newsletter\Entities
 */
class Template extends TenancyModel
{
    use SoftDeletes;
    /**
     * @var string
     */
    protected $table = 'newsletter_templates';
    /**
     * @var array
     */
    protected $fillable = ['name', 'created_by', 'description'];
    /**
     * @var bool
     */
    protected $softDelete = true;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function newsletter()
    {
        return $this->belongsTo('Modules\Newsletter\Entities\Newsletter', 'id', 'template_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blocks()
    {
        return $this->hasMany('Modules\Newsletter\Entities\TemplateBlock', 'template_id');
    }
    /**
     * Always format date
     */
    public function getUpdatedAtAttribute($value) {
        $lang = session()->has('lang') ? session()->get('lang') : "FR";
        if($lang=='FR'){
            setlocale(LC_TIME, 'fr_FR');
            $date=Carbon::parse($value)->format('F d,Y');
            $date_fr = ['janvier','février','mars','avril','mai','juin','juillet','aout','septembre','octobre','novembre','décembre'];
            $dateEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November','December'];
            return str_replace($dateEn, $date_fr, $date);
        }
        else{
            return Carbon::parse($value)->format('F d,Y');
        }
    }
}