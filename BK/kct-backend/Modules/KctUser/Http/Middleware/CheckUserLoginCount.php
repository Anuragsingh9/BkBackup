<?php

namespace Modules\KctUser\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserLoginCount extends \Modules\UserManagement\Http\Middleware\CheckUserLoginCount
{

}
