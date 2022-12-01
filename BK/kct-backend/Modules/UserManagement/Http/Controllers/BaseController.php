<?php

namespace Modules\UserManagement\Http\Controllers;

use Illuminate\Routing\Controller;

use Modules\UserManagement\Repositories\BaseRepository;
use Modules\UserManagement\Services\BaseService;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is module base controller which will contain all the dependencies with injected already
 * Following types of dependencies will be injected here
 *
 * 1. Service Dependencies
 * 2. Repository Dependencies
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class BaseController
 * @package Modules\KctAdmin\Http\Controllers
 */
class BaseController extends Controller {
    protected BaseRepository $repo;
    protected BaseService $services;

    public function __construct(
        BaseService $services,
        BaseRepository $repo
    ) {
        $this->services = $services;
        $this->repo = $repo;
    }
}
