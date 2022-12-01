<?php

namespace Modules\KctUser\Http\Controllers\V1;

use Hyn\Tenancy\Environment;
use Illuminate\Routing\Controller;
use Modules\KctUser\Repositories\BaseRepo;
use Modules\KctUser\Services\BaseService;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is module base controller which will contain all the dependencies with injected already
 * Following types of dependencies will be injected here
 *
 * 1. Service Dependencies
 * 2. Repository Dependencies
 * 3. Business Service Dependencies
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class BaseController
 * @package Modules\SuperAdmin\Http\Controllers
 */
class BaseController extends Controller {
    protected BaseService $services;
    protected BaseRepo $repo;
    protected Environment $tenant;

    public function __construct(
        BaseService $services,
        BaseRepo $repo
    ) {
        $this->services = $services;
        $this->repo = $repo;
        $this->tenant = app(Environment::class);
    }

}
