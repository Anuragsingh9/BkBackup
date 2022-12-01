<?php

    namespace App\Model;

    // use Illuminate\Database\Eloquent\Model;
    use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

    class UserSkill extends TenancyModel
    {
        protected $casts = [
            'setting' => 'array',
        ];
    protected $connection= 'maria';
        protected $fillable =
            [
                'user_id',
                'skill_id',
                'created_by',
                'mandatory_checked_by',
                'mandatory_checked_at',
                'checkbox_input',
                'yes_no_input',
                'scale_5_input',
                'scale_10_input',
                'percentage_input',
                'numerical_input',
                'text_input',
                'comment_text_input',
                'address_text_input',
                'long_text_input',
                'file_input',
                'mandatory_checkbox_input',
                'mandatory_file_input',
                'mandatory_acceptance_input',
                'select_input',
                'date_input',
                'field_id',
                'type',
                'for_card_instance',
                'setting',
            ];

        public function getAddressTextInputAttribute($value)
        {
            if (!empty($value) && isJson($value)) {
                $decode = json_decode($value, TRUE);
                return isset($decode['address']) ? $decode['address'] : $value;
            } else {
                return $value;
            }
        }

    }
