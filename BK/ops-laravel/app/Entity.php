<?php

namespace App;

//use Illuminate\Database\Eloquent\Model;
use App\Model\TaskComment;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Modules\Crm\Entities\CrmNote;
use Modules\Crm\Entities\CrmDocument;
use Modules\Crm\Entities\CrmTask;
use Modules\Crm\Entities\AssistanceReport;
class Entity extends TenancyModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'long_name',
        'short_name',
        'address1',
        'zip_code',
        'city',
        'country',
        'phone',
        'email',
        'fax',
        'entity_description',
        'entity_website',
        'industry_id',
        'entity_sub_type',
            'address2',
        'entity_logo',
        'entity_type_id',
        'created_by',
        'is_active',
        'entity_ref_type',
        'is_internal',
        'membership_type', 'entity_label'

    ];

    protected $hiddenFields = [
        'entity_ref_type',
        'is_internal',
        'entity_logo',
        'entity_description',
        'industry_id',
        'is_active',
        'created_by',
        'entity_type_id',
        'address2',
       // 'fax',

    ];
    protected $table = 'entities';


    /**
     * @return array
     */
    public function getFillables()
    {
        $fillable = array_diff($this->fillable, $this->hiddenFields);
        return $fillable;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->table;
    }
    public function entityLabel()
    {
        return $this->hasOne('App\EntityUser', 'entity_id', 'id');
    }


    /**
     * Get all of the Entity's notes.
     */
    public function notes()
    {
        return $this->morphMany(CrmNote::class, 'crm_noteable');
    }

    /**
     * Get all of the Entity's document.
     */
    public function documents()
    {
        return $this->morphMany(CrmDocument::class, 'crm_documentable')->with(['regularDocument']);
    }
    /**
     * Get all of the Entity's comments.
     */
    public function comments()
    {
        return $this->morphMany(TaskComment::class, 'commentable');
    }
    public function industry()
    {
        return $this->hasOne('App\Industry', 'id', 'industry_id');
    }
    public function entityUser()
    {
        return $this->hasMany('App\EntityUser', 'entity_id', 'id');
    }
    public function entityAdmin()
    {
        return $this->hasMany('App\UnionAdmin', 'id', 'entity_id');
    }

    public function user()
    {
        return $this->belongsToMany('App\User', 'entity_users');
    }
    
    public function contact()
    {
        return $this->belongsToMany('App\Contact', 'entity_users');
    }

    /**
     * Get all of the user's tasks.
     */
    public function tasks()
    {
        return $this->morphMany(CrmTask::class, 'crm_object_tasksable');
    }
    /**
     * Get all of the user's notes.
     */
    public function assistance_reports()
    {
        return $this->morphMany(AssistanceReport::class, 'assistance_reportable');
    }

    public function entityType() {
        return $this->hasOne('App\EntityType', 'id', 'entity_type_id');
    }

    public function allTasks() {
        return $this->morphToMany('App\Task', 'crm_object_tasksable', 'crm_object_tasks');
    }
}
