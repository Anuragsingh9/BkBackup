<?php

namespace Modules\KctAdmin\Http\Controllers\V1;

use Hyn\Tenancy\Environment;
use Illuminate\Routing\Controller;
use Modules\KctAdmin\Repositories\BaseRepo;
use Modules\KctAdmin\Services\BaseService;
use Modules\KctAdmin\Traits\KctHelper;

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
    use KctHelper;

    // Common Variable
    protected Environment $tenant;

    protected BaseService $services;
    protected BaseRepo $repo;

    public function __construct(
        BaseService $services,
        BaseRepo    $repo
    ) {
        $this->services = $services;
        $this->repo = $repo;
        $this->tenant = app(Environment::class);
    }
}
