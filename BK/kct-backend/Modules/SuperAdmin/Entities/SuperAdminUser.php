<?php

namespace Modules\SuperAdmin\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property string fname
 * @property string lname
 * @property string email
 * @property string password
 * @property string avatar
 * @property int login_count
 *
 * Class SuperAdminUser
 * @package Modules\SuperAdmin\Entities
 */
class SuperAdminUser extends Authenticatable {
    use HasFactory;

    protected $table = 'users';

    protected $fillable = ['fname', 'lname', 'email', 'password', 'avatar', 'login_count'];

}
