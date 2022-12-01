<?php

namespace Modules\Newsletter\Entities;
use Carbon\Carbon;
// use Illuminate\Database\Eloquent\Model;0
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsletterBlock extends TenancyModel
{
    protected $table = 'newsletter_blocks';

    protected $fillable = ['newsletter_id','template_block_id','blocks','image_url','short_order'];
    
    use SoftDeletes;

    public function newsletter()
    {
        return $this->hasOne('Modules\Newsletter\Entities\Newsletter','id','newsletter_id');
    }
    public function templateBlock()
    {
        return $this->hasOne('Modules\Newsletter\Entities\TemplateBlock','id','template_block_id');
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
