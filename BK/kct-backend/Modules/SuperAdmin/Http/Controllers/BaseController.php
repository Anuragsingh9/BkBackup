<?php


namespace Modules\SuperAdmin\Http\Controllers;


use Hyn\Tenancy\Environment;
use Illuminate\Routing\Controller;
use Modules\SuperAdmin\Repositories\BaseRepo;
use Modules\SuperAdmin\Repositories\IAccountRepository;
use Modules\SuperAdmin\Repositories\IOrganisationRepository;
use Modules\SuperAdmin\Repositories\ISuOtpRepository;
use Modules\SuperAdmin\Repositories\ITagRepository;
use Modules\SuperAdmin\Services\BaseService;
use Modules\SuperAdmin\Services\BusinessServices\IAccountService;
use Modules\SuperAdmin\Services\BusinessServices\IEmailService;
use Modules\SuperAdmin\Services\DataServices\IExportService;
use Modules\SuperAdmin\Services\DataServices\ITempDataService;
use Modules\SuperAdmin\Services\OtherModuleCommunication\IKctAdminCommunication;

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

    // Common Variable
    protected Environment $tenant;

    // Repositories
    protected BaseService $services;
    protected BaseRepo $repo;


    public function __construct(
        BaseService $services,
        BaseRepo $repo
    ) {
        $this->services = $services;
        $this->repo = $repo;
        $this->tenant = app(Environment::class);
    }
}
