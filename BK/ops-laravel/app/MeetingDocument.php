<?php

namespace App;

//use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Abstracts\TenantModel as TenancyModel;

class MeetingDocument extends TenancyModel
{
    public $table = 'meeting_document';
}
