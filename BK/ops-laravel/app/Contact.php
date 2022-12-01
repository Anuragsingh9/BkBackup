<?php

namespace App;

use Carbon\Carbon;
// use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends TenancyModel
{
    
    protected $fillable = [
        'fname', 'lname', 'email', 'phone', 'mobile', 'address', 'postal', 'city', 'country', 'status'
    ];
    protected $table = 'newsletter_contacts';

    use SoftDeletes;
    protected $hiddenInApi = [
        'status',
    ];
    public function lists()
    {
        return $this->morphToMany('App\ListModel', 'listablesls');
    }

    /**
     * Always format date
     */
    public function getUpdatedAtAttribute($value)
    {
        $lang = session()->has('lang') ? session()->get('lang') : "FR";
        if ($lang == 'FR') {
            setlocale(LC_TIME, 'fr_FR');
            $date = Carbon::parse($value)->format('F d,Y');
            $date_fr = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'aout', 'septembre', 'octobre', 'novembre', 'décembre'];
            $dateEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            return str_replace($dateEn, $date_fr, $date);
        } else {
            return Carbon::parse($value)->format('F d,Y');
        }
    }

    public function getFillables()
    {
        return $this->fillable;
    }

    public function getFillablesPerson()
    {
        $user = new User();
        $userFields = $user->getFillables();
        $hiddenFields = array_merge($userFields, $this->hiddenInApi);
        $fillable = array_diff($this->fillable, $hiddenFields);
        return $fillable;
    }

    public function getTableName()
    {
        return $this->table;
    }

    public function entity()
    {
        return $this->belongsToMany('App\Entity', 'entity_users', 'contact_id', 'entity_id');
    }
    public function entityUser()
    {
        return $this->hasMany('App\EntityUser', 'contact_id', 'id')->with(['entity']);
    }
    
}
