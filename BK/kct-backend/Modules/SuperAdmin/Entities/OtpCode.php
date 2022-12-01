<?php

namespace Modules\SuperAdmin\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string code
 * @property string otp_type
 * @property string email
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class OtpCode extends Model {
    use HasFactory;

    protected $fillable = [
        'code',
        'otp_type',
        'email'
    ];
}
