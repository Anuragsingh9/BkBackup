<?php

namespace Modules\Qualification\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class ReviewStep extends TenancyModel
{
    protected $fillable = [
        'opinion',
        'opinion_text',
        'user_id',
        'opinion_by',
        'opinion_by_user',
        'step_id',
        'saved_for',
        'for_card_instance',
    ];
    protected $table = 'qualification_experts_review_steps';

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'opinion_by_user')->select('id','fname','lname','email');
    }

    public function step()
    {
        return $this->hasOne('Modules\Qualification\Entities\Step', 'id', 'step_id');
    }
}
