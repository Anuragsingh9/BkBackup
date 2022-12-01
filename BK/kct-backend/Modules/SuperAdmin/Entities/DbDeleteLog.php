<?php

namespace Modules\SuperAdmin\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DbDeleteLog extends Model {
    use HasFactory;

    protected $fillable = [
        'fqdn',
        'db_name',
        'db_file_path',
    ];

}
