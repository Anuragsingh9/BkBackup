<?php

namespace Modules\Newsletter\Entities;

// use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateBlock extends TenancyModel
{
    protected $table = 'newsletter_template_blocks';

    protected $fillable = ['template_id','block_html_code','image_url','sort_order'];

    use SoftDeletes;

    public function template()
    {
        return $this->hasOne('Modules\Newsletter\Entities\Template','id','template_id');
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
