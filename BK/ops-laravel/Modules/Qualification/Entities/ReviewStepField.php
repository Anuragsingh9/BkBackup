<?php

namespace Modules\Qualification\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class ReviewStepField extends TenancyModel
{
    //opinion_by '0->Expert,1->WkAdmin,2->ORG'
    //opinion '0->questionMark,1->checkMark,2->crossMark'
    protected $fillable = [
        'step_id',
        'field_id',
        'opinion',
        'user_id',
        'opinion_by',
        'opinion_by_user',
        'saved_for',
        'for_card_instance',
    ];
    protected $table = 'qualification_experts_review_fields';
}
