<?php

    namespace Modules\Resilience\Entities;

    use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use App\Traits\UseUuid;

    class ConsultationSignUpClassPosition extends TenancyModel
    {
        use SoftDeletes;

        protected $table = 'consultation_signup_class_positions';
        protected $fillable = [
            'consultation_sign_up_class_uuid',
            'positions',
            'sort_order',
            'class_type',
        ];
    }
