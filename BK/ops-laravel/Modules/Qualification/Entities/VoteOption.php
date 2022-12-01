<?php

namespace Modules\Qualification\Entities;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoteOption extends TenancyModel
{

    protected $fillable = ['vote_id','option_name','short_name','description','option_color','option_tip_text','short_order'];
    protected $table = 'votes_options';
    use SoftDeletes;
    public function vote()
    {
        return $this->belongsTo('Modules\Qualification\Entities\Vote','vote_id');
    }
}
