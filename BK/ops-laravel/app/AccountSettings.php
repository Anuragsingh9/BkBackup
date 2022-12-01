<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

//use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
class AccountSettings extends Model
{
    protected $casts = [
        'setting' => 'array'
    ];
    public $fillable = ['account_id', 'test_version', 'date_from', 'date_to', 'light_version', 'travel_enable', 'mobile_enable', 'email_enabled', 'wvm_enable', 'fvm_enable', 'user_group_enable', 'wiki_enable', 'reminder_enable', 'zip_download', 'fts_enable', 'repd_connect_mode', 'prepd_repd_notes', 'project_enable', 'multiLoginEnabled', 'custom_profile_enable', 'meeting_meal_enable', 'notes_to_secretary_enable', 'import_enable', 'new_member_alert',
        'survey_menu_enable',
        'newsletter_menu_enable',
        'elearning_menu_enabled',
        'crm_menu_enable',
        'reseau_menu_enable',
        'wiki_menu_enable',
        'piloter_menu_enable',
        'setting',
    ];


}
