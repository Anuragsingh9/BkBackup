<?php

namespace Modules\Resilience\Entities;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
use Illuminate\Database\Eloquent\SoftDeletes;


class ConsultationStepAsset extends TenancyModel
{
    use SoftDeletes;

    protected $table = 'consultation_step_assets';

    protected $fillable = [
        'consultation_step_id',
        'title',
        'info_type',
        'media_link',
        'pdf',
        'allow_download_pdf'
    ];

    public function consultationStep() {
        return $this->belongsTo('Modules\Resilience\Entities\ConsultationStep')->withoutGlobalScopes();
    }
}
