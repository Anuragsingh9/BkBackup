<?php

    namespace Modules\Resilience\Entities;

    use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use App\Traits\UseUuid;

    class ConsultationSignUpClass extends TenancyModel
    {
        use SoftDeletes, UseUuid;
        protected $casts = [
            'label_setting' => 'array'
        ];
        protected $table = 'consultation_signup_classes';
        protected $primaryKey = 'uuid';
        protected $fillable = [
            'label',
            'class_type',
            'label_setting',
            'sort_order',
        ];

        /*
         * relationship of classes->Positions
         * */
        public function positions()
        {
            return $this->hasMany('Modules\Resilience\Entities\ConsultationSignUpClassPosition')->whereNull('deleted_at');
        }
    }
