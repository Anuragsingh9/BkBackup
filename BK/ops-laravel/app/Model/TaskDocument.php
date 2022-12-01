<?php

namespace App\Model;

use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class TaskDocument extends TenancyModel
{
    public $fillable = [
        'task_id',
        'document_id',
        'created_by_id',
    ];

    public function document()
    {
        return $this->hasOne('App\RegularDocument', 'id', 'document_id')->select(['id', 'document_title', 'document_file']);
    }
}
