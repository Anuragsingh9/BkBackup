<?php

namespace Modules\Qualification\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class CandidateCard extends TenancyModel
{
    protected $fillable = ['user_id', 'card_instance', 'card_no', 'date_of_validation', 'final_by', 'workshop_id', 'is_archived', 'card_no', 'reason_card_archived','review_done','setting'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

}
