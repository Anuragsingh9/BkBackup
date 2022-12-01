<?php

namespace App;

use App\Scopes\WorkshopScope;
use Auth;
//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Modules\Messenger\Entities\WorkshopTopic;

class Workshop extends TenancyModel
{
    protected $casts = [
        'setting' => 'array',
        'signatory' => 'array'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new WorkshopScope);
    }

    public $fillable = array('validator_id', 'workshop_name', 'workshop_desc', 'code1', 'code2', 'workshop_type', 'president_id', 'is_private', 'display', 'is_qualification_workshop', 'setting', 'signatory', 'is_dependent');

    public function meta()
    {
        return $this->hasMany('App\WorkshopMeta');
    }

    public function meta_data()
    {
        return $this->hasMany('App\WorkshopMeta');
    }

    public function code()
    {
        return $this->hasOne('App\WorkshopCode');
    }

    public function meetings()
    {
        return $this->hasMany('App\Meeting', 'workshop_id', 'id');
    }

    public function workshop_meta()
    {
        return $this->hasMany('App\WorkshopMeta')->select(['id', 'role', 'workshop_id', 'user_id']);
    }

    public function meta_by_role()
    {
        return $this->hasMany('App\WorkshopMeta')->whereIn('role', [1, 2])->select(['id', 'role', 'workshop_id', 'user_id']);
    }

    public function task()
    {
        return $this->hasMany('App\Task', 'workshop_id', 'id')->where('status', 1);
    }

    public function document()
    {
        return $this->hasMany('App\RegularDocument', 'workshop_id', 'id');
    }

    public function validator()
    {
        return $this->hasOne('App\User', 'id', 'validator_id');
    }

    public function president()
    {
        return $this->hasOne('App\User', 'id', 'president_id');
    }

    public function projects()
    {
        return $this->hasMany('App\Project', 'wid', 'id')->orderBy('id', 'ASC');
    }

    public function scopeQualification($query)
    {
        return $query->OrWhereNull('is_qualification_workshop')->OrWhere('is_qualification_workshop', 0);
    }
    
    public function membersCount() {
        return $this->hasOne('App\WorkshopMeta', 'workshop_id')
            ->selectRaw('workshop_id,COUNT(DISTINCT user_id) as members')
            ->groupBy('workshop_id');
    }
    public function messages()
    {
        return $this->hasMany('App\Message', 'workshop_id', 'id');
    }
    
    public function categories()
    {
        return $this->hasMany('App\MessageCategory', 'workshop_id', 'id');
    }
    
    public function messageCategories()
    {
        return $this->belongsToMany('App\MessageCategory', 'messages', 'workshop_id', 'category_id');
    }
    public function imTopics() {
        return $this->hasMany(WorkshopTopic::class, 'workshop_id', 'id');
    }
    
    public function parentWorkshop()
    {
        return $this->hasOne(Workshop::class, 'code1', 'code1')->where(['workshop_type' => 1, 'is_dependent' => 1]);
    }
    
    public function dependentWorkshop()
    {
        return $this->hasMany(Workshop::class, 'code1', 'code1')->where(['workshop_type' => 2, 'is_dependent' => 0])->with('parentWorkshop');
    }
}
