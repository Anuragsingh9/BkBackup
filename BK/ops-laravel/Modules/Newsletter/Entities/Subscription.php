<?php

namespace Modules\Newsletter\Entities;
use Carbon\Carbon;
// use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends TenancyModel
{
    protected $fillable = [
        'form_name', 'list_id', 'success_url',
        'error_url', 'display_header_zone',
        'title', 'seperator_line_color', 'field_email',
        'field_fname', 'field_lname',
        'font_family', 'font_size', 'background_color',
        'button_color', 'button_text_color',
        'rounded_button', 'button_text', 'html_code','html_form'
    ];
    protected $table = 'newsletter_subscription_forms';


    use SoftDeletes;

    public function list()
    {
        return $this->hasOne('App\Model\ListModel', 'id', 'list_id');
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
