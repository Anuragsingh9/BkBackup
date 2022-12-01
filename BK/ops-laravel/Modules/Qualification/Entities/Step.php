<?php

namespace Modules\Qualification\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class Step extends TenancyModel
{
    //we are using is_final_step as is Domain or not
    protected $table = 'qualification_steps';
    protected $fillable = ['name', 'description', 'is_conditional', 'is_final_step', 'button_text', 'sort_order'];
    use SoftDeletes;

    public function fields()
    {
        return $this->belongsToMany('App\Model\Skill', 'qualification_fields', 'step_id', 'field_id')->whereNull('deleted_at')->withPivot('id','sort_order')->orderByRaw('CAST(qualification_fields.sort_order AS UNSIGNED) ASC');
    }

    public function conditional()
    {
        return $this->hasOne('Modules\Qualification\Entities\StepConditional', 'step_id');
    }

    public function stepFields()
    {
        return $this->hasMany('Modules\Qualification\Entities\Field', 'step_id');
    }

    public function stepReview()
    {
        return $this->hasMany('Modules\Qualification\Entities\ReviewStep', 'step_id');
    }


    public function stepReviewYellow()
    {
        return $this->hasMany('Modules\Qualification\Entities\ReviewStep', 'step_id')->where('opinion', 1);
    }

    public function stepReviewRed()
    {
        return $this->hasMany('Modules\Qualification\Entities\ReviewStep', 'step_id')->where('opinion', 2);
    }


    public function domainCheckbox()
    {
        return $this->belongsToMany('App\Model\Skill', 'qualification_domain_checkboxes', 'step_id', 'skill_id')->withPivot('id');
    }

    public function domainCheckboxSingle()
    {
        return $this->hasOne('Modules\Qualification\Entities\DomainCheckbox', 'step_id');
    }
}
