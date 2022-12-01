<?php

namespace App\Model;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

//use Illuminate\Database\Eloquent\Model;

class Skill extends TenancyModel
{

    protected $connection= 'maria';
    protected $fillable =
        [
            'name',
            'skill_tab_id',
            'short_name',
            'description',
            'image',
            'is_valid',
            'is_mandatory',
            'skill_format_id',
            'is_unique',
            'comment',
            'link_text',
            'comment_link',
            'comment_target_blank',
            'sort_order',
            'is_conditional',
            'is_qualifying',
            'tooltip_en',
            'tooltip_fr',
        ];

    public function skillTab()
    {
        return $this->hasOne('App\Model\SkillTabs', 'id', 'skill_tab_id');
    }

    public function skillFormat()
    {
        return $this->hasOne('App\Model\SkillTabFormat', 'id', 'skill_format_id');
    }

    public function skillImages()
    {
        return $this->hasOne('App\Model\SkillImage', 'skill_id');
    }

    public function skillSelect()
    {
        return $this->hasMany('App\Model\SelectOption', 'skill_id')->orderByRaw('CAST(sort_order AS UNSIGNED) ASC');
    }

    public function skillCheckBox()
    {
        return $this->hasOne('App\Model\MandatoryCheckboxe', 'skill_id')->where('type_of',0);
    }
    public function skillCheckBoxAcceptance()
    {
        return $this->hasOne('App\Model\MandatoryCheckboxe', 'skill_id')->where('type_of',1);
    }

    public function skillMeta()
    {
        return $this->hasOne('App\Model\SkillMeta', 'skill_id');
    }

    public function userSkill()
    {
        return $this->hasOne('App\Model\UserSkill', 'skill_id')->orderByDesc('id');
    }
    public function UserHaveManySkills()
    {
        return $this->hasMany('App\Model\UserSkill', 'skill_id')->orderByRaw('CAST(user_skills.id AS UNSIGNED) DESC');
    }
    public function customSkill()
    {
        return $this->hasOne('App\Model\UserSkill', 'skill_id');
    }

    //for multiple users for skill as above used for many places so use this
    public function allUserSkills()
    {
        return $this->hasMany('App\Model\UserSkill', 'skill_id');
    }
    public function conditionalSkill()
    {
        return $this->hasOne('App\Model\ConditionalSkill', 'conditional_field_id');
    }

    public function skillField()
    {
        return $this->belongsToMany('Modules\Qualification\Entities\Step', 'qualification_fields', 'field_id', 'step_id')->whereNull('qualification_fields.deleted_at')->withPivot('id');
    }

    public function fieldReview()
    {
        return $this->hasOne('Modules\Qualification\Entities\ReviewStepField', 'field_id');
    }

    public function domainCheckbox()
    {
        return $this->hasOne('Modules\Qualification\Entities\DomainCheckbox', 'skill_id');
    }
}
