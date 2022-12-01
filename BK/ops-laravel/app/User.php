<?php

namespace App;

use App\Model\TaskComment;
use App\Model\UserSkill;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Modules\Cocktail\Entities\EventTag;
use Modules\Cocktail\Entities\EventTagMata;
use Modules\Cocktail\Entities\EventUser;
use Modules\Cocktail\Entities\EventUserPersonalInfo;
use Modules\Cocktail\Entities\EventUserTagRelation;
use Modules\Cocktail\Entities\UserVisibility;
use Modules\Crm\Entities\CrmNote;
use Modules\Crm\Entities\AssistanceReport;
use Modules\Crm\Entities\CrmDocument;
use Auth;
use Carbon\Carbon;
use Modules\Crm\Entities\CrmTask;
use Modules\Events\Entities\Event;
use Modules\Events\Http\Middleware\SaveUserMeta;
use Modules\Messenger\Entities\Channel;

class User extends Authenticatable {
    protected $connection = 'tenant';
    protected $table = 'users';
    use HasApiTokens, Notifiable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'fname', 'lname', 'phone', 'mobile', 'address', 'postal', 'city', 'country', 'role', 'hash_code', 'login_code', 'fcm_token', 'internal_id', 'import_email', 'password', 'import_member_email', 'permissions', 'sub_role', 'on_off', 'phone', 'postal', 'address', 'city', 'country', 'created_at',
        'role_commision', //
        'role_wiki', //
        'function_union', //
        'society', //
        'function_society', //
        'family_id', //
        'industry_id', //
        'union_id', //
        'avatar' , //
        'remember_token', //
        'identifier', //
        'login_count', //
        'setting', //
        'is_dummy',//
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    protected $casts = [
        'permissions' => 'array',
    ];
    
    protected $hiddenInApi = [
        'password',
        'role',
        'hash_code',
        'login_code',
        'fcm_token',
        'internal_id',
        'import_email',
        'import_member_email',
        'permissions',
        'on_off',
        'sub_role',
        'created_at',
    ];
    // public function role_permission(){
    //     return $this->hasMany('App\RolePermission','    ')
    // }

    public function userVisibility(){
        return $this->hasOne(UserVisibility::class);
    }
    
    function wiki() {
        return $this->hasMany('App\Wiki', 'added_by', 'id');
    }
    
    function doodleVote() {
        return $this->hasMany('App\DoodleVote', 'user_id', 'id');
    }
    
    function union() {
        return $this->hasMany('App\Union', 'id', 'union_id');
    }
    
    function role() {
        return $this->hasOne('App\Role', 'role_key', 'role');
    }
    
    public function userMeta() {
        return $this->hasMany('App\WorkshopMeta', 'user_id', 'id');
    }
    
    public function entityUser() {
        return $this->hasMany('App\EntityUser', 'contact_id', 'id')->with(['entity']);
    }
    
    public function entity() {
        return $this->belongsToMany('App\Entity', 'entity_users')->withPivot(['id','user_id','entity_id','entity_label']);
    }
    
    public function lists() {
        return $this->morphToMany('App\Model\ListModel', 'listablesls');
    }
    
    public function getFillables() {
        return $this->fillable;
    }
    
    public function getFillablesPerson() {
        $contact = new Contact();
        $contactFields = $contact->getFillables();
        $hiddenFields = array_merge($contactFields, $this->hiddenInApi);
        $fillable = array_diff($this->fillable, $hiddenFields);
        return $fillable;
    }
    
    public function getTableName() {
        return $this->table;
    }
    
    /**
     * Get all of the user's notes.
     */
    public function notes() {
        return $this->morphMany(CrmNote::class, 'crm_noteable');
    }
    
    /**
     * Get all of the user's notes.
     */
    public function documents() {
        return $this->morphMany(CrmDocument::class, 'crm_documentable')->with(['regularDocument']);
    }
    
    /**
     * Get all of the user's comments.
     */
    public function comments() {
        return $this->morphMany(TaskComment::class, 'commentable');
    }
    
    function userSkills() {
        return $this->hasMany('App\Model\UserSkill');
    }
    
    public static $preventAttrGet = true;
    
    public function getCreatedAtAttribute($value) {
        // if($this->preventAttrGet){
        $lang = session()->has('lang') ? session()->get('lang') : "FR";
        //        $lang = Auth::check() ? json_decode(Auth::user()->setting)->lang : "FR";
        if ($lang == 'FR') {
            setlocale(LC_TIME, 'fr_FR');
            $date = Carbon::parse($value)->format('F d, Y');
            $date_fr = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'aout', 'septembre', 'octobre', 'novembre', 'décembre'];
            $dateEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            return str_replace($dateEn, $date_fr, $date);
        } else {
            return Carbon::parse($value)->format('F d, Y');
        }
        // }
        // else{
        //     return $value;
        // }
    }
    
    public function getPostalAttribute($value) {
        
        if (strlen($value) >= 5) {
            
            return $value;
        } else {
            
            if (strlen($value) == 0) {
                return '';
            } else {
                return str_pad($value, 5, "0");
            }
        }
    }
    
    public function userInfo() {
        return $this->hasOne('App\Model\UserMeta', 'user_id', 'id');
    }
    
    public function userSkillCompany() {
        return $this->hasOne('App\Model\UserSkill', 'field_id', 'id')
            ->whereNotNull('text_input')
            ->where('type', 'candidate');
    }
    
    public function expertReview() {
        return $this->hasMany('Modules\Qualification\Entities\ReviewStep', 'opinion_by_user')->where('opinion_by', 0);
    }
    
    /**
     * Get all of the user's tasks.
     */
    public function tasks() {
        return $this->morphMany(CrmTask::class, 'crm_object_tasksable');
    }
    
    public function userCards() {
        return $this->hasMany('Modules\Qualification\Entities\CandidateCard', 'user_id');
    }
    
    public function userFirstReminder() {
        return $this->hasMany('Modules\Qualification\Entities\QualificationUserReminder', 'user_id')
            ->where('type_of_email', 2);
    }
    
    public function userFourthReminder() {
        return $this->hasMany('Modules\Qualification\Entities\QualificationUserReminder', 'user_id')
            ->where('type_of_email', 3);
    }
    
    public function userRegistrationReminder() {
        return $this->hasMany('Modules\Qualification\Entities\QualificationUserReminder', 'user_id')
            ->where('type_of_email', 5);
    }
    
    /**
     * Get the user that owns the phone.
     */
    public function workshop() {
        return $this->hasOne('App\WorkshopMeta', 'user_id', 'id')->where('role', 4);
    }
    
    public function userSkillSiret() {
        return $this->hasOne('App\Model\UserSkill', 'field_id', 'id')
            ->whereNotNull('numerical_input')
            ->where('type', 'candidate');
    }
    
    public function events() {
        return $this->morphToMany('Modules\Events\Entities\Event', 'eventable');
    }
    
    /**
     * Get all of the user's reports.
     */
    public function assistance_reports() {
        return $this->morphMany(AssistanceReport::class, 'assistance_reportable');
    }
    
    public function messages() {
        return $this->hasMany('App\Messages', ['user_id', 'to_id']);
    }
    
    public function allTasks() {
        return $this->morphToMany('App\Task', 'crm_object_tasksable', 'crm_object_tasks');
    }
    
    /**
     *
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    
    public function scopeCandidate($query) {
        return $query->where(function ($a) {
            $a->where('sub_role', '!=', 'C1');
            $a->orWhereNull('sub_role');
        });
    }
    
    public function channels() {
        return $this->belongsToMany(Channel::class);
    }
    
    public function unions() {
        return $this->belongsToMany(Entity::class, 'entity_users', 'user_id',
            'entity_id', 'id', 'id')
            ->where('entity_type_id', 3)
            ->withPivot('entity_label')
            ->orderBy('entity_users.id', 'asc');
    }
    
    public function companies() {
        return $this->belongsToMany(Entity::class, 'entity_users', 'user_id',
            'entity_id', 'id', 'id')
            ->where('entity_type_id', 2)
            ->withPivot('entity_label');
    }
    
    public function instances() {
        return $this->belongsToMany(Entity::class, 'entity_users', 'user_id',
            'entity_id', 'id', 'id')
            ->where('entity_type_id', 1)
            ->withPivot('entity_label');
    }
    public function presses() {
        return $this->belongsToMany(Entity::class, 'entity_users', 'user_id',
            'entity_id', 'id', 'id')
            ->where('entity_type_id', 4)
            ->withPivot('entity_label');
    
    }
    
    public function socialLink() {
        return $this->hasOne('Modules\Crm\Entities\UserSocialAccountLink', 'user_id', 'id');
    }
    
    public function facebookUrl() {
        return $this->socialLink()->where('channel', 'facebook')->orderBy('is_main', 'desc');
    }
    public function twitterUrl() {
        return $this->socialLink()->where('channel', 'twitter')->orderBy('is_main', 'desc');
    }
    public function instagramUrl() {
        return $this->socialLink()->where('channel', 'instagram')->orderBy('is_main', 'desc');
    }
    public function linkedinUrl() {
        return $this->socialLink()->where('channel', 'linkedin')->orderBy('is_main', 'desc');
    }
    public function virtualEvents() {
        return $this->belongsToMany(Event::class, 'event_user_data', 'user_id', 'event_uuid', 'id' ,'event_uuid','event_uuid')
            ->where("type", 'virtual')
            ->withPivot('is_presenter', 'is_moderator')
            ->withCount('isHostOfAnySpace')
            ->with('users')
            ;
    }
    
    public function eventUser() {
        return $this->belongsTo(EventUser::class, 'id', 'user_id');
    }

    public function tags()
    {
        return $this->hasMany(EventTagMata::class, 'user_id')->select(['id','user_id','tag_id'])->with('tag:name,id');
    }
    
    public function eventUsedTags() {
        return $this->belongsToMany(EventTag::class,
            'event_tag_metas',
            'user_id', // event_meta_tag.[foreignPivotKey] in (select result of users.[parentKey])
            'tag_id', //on event_tag.[relatedKey] = event_tag_meta.[relatedPivotKey]
            'id', // id of user table
            'id' //on event_tag.[relatedKey] = event_tag_meta.[relatedPivotKey]
        )
            ->where('is_display', 1)
            ->orderBy('name','asc')
            ;
    }
    
    public function personalInfo() {
        return $this->hasOne(EventUserPersonalInfo::class, 'user_id', 'id');
    }
    
    public function tagsRelationForPP() {
        return $this->hasMany(EventUserTagRelation::class, 'user_id', 'id');
    }
}
