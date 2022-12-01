<?php

namespace App;

//use Illuminate\Database\Eloquent\Model;
use App\Scopes\ProjectScope;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class Project extends TenancyModel
{
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ProjectScope);
    }

    public $fillable = array(
        'project_label',
        'user_id',
        'wid',
        'color_id',
        'end_date',
        'is_default_project',
        'display',
        'email_disable',
        'setting',
        'project_description',
        'project_goal',
        'project_result',
    );

    public function milestone()
    {
        return $this->hasMany('App\Milestone', 'project_id', 'id')->orderBy('end_date', 'ASC');
    }

    public function workshop()
    {
        return $this->hasOne('App\Workshop', 'id', 'wid');
    }

    public function user_permission()
    {
        return $this->hasMany('App\Model\UserTaskPermission', 'project_id', 'id')->where('status', 0);
    }

    public function project_timeline_order()
    {
        return $this->hasOne('App\Model\ProjectTimelineOrder', 'project_id', 'id')->select(['id', 'project_id', 'order']);
    }
}