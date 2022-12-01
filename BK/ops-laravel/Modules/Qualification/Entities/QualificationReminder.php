<?php
    
    namespace Modules\Qualification\Entities;
    
    use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;
    
    class QualificationReminder extends TenancyModel
    {
        protected $casts = [
            'week_reminder' => 'array',
        ];
        protected $fillable = ['section_id', 'reminder_time', 'comment', 'week_reminder'];
    }
