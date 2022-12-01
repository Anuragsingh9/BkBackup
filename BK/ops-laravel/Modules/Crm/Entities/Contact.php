<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 6/22/2019
 * Time: 3:25 PM
 */
namespace Modules\Crm\Entities;
use App\Model\TaskComment;
use Carbon\Carbon;
// use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Crm\Entities\CrmDocument;
use Modules\Crm\Entities\CrmNote;
use Modules\Crm\Entities\CrmTask;
use Modules\Crm\Entities\AssistanceReport;
class Contact extends TenancyModel
{
    protected $fillable = [
        'fname', 'lname', 'email', 'phone', 'mobile', 'address', 'postal', 'city', 'country', 'status'
    ];
    protected $table = 'newsletter_contacts';
    protected $hiddenInApi = [
        'status',
    ];
    use SoftDeletes;
    public function lists()
    {
        return $this->morphToMany('App\ListModel', 'listablesls');
    }
    /**
     * Always format date
     */
    public function getUpdatedAtAttribute($value) {
        return Carbon::parse($value)->format('F d,Y');
    }

    /**
     * Get all of the contact's notes.
     */
    public function notes()
    {
        return $this->morphMany(CrmNote::class, 'crm_noteable');
    }
    
/**
     * Get all of the contact's document.
     */
    public function documents()
    {
        return $this->morphMany(CrmDocument::class, 'crm_documentable')->with(['regularDocument']);
    }
    /**
     * Get all of the contact's comments.
     */
    public function comments()
    {
        return $this->morphMany(TaskComment::class, 'commentable');
    }
    /**
     * Get all of the user's tasks.
     */
    public function tasks()
    {
        return $this->morphMany(CrmTask::class, 'crm_object_tasksable');
    }
    /**
     * Get all of the user's reports.
     */
    public function assistance_reports()
    {
        return $this->morphMany(AssistanceReport::class, 'assistance_reportable');
    }
}