<?php

namespace Modules\KctAdmin\Services;

use Modules\KctAdmin\Repositories\BaseRepo;
use Modules\KctAdmin\Services\BusinessServices\IAnalyticsService;
use Modules\KctAdmin\Services\BusinessServices\IColorExtractService;
use Modules\KctAdmin\Services\BusinessServices\IEmailService;
use Modules\KctAdmin\Services\BusinessServices\IEventService;
use Modules\KctAdmin\Services\BusinessServices\IFileService;
use Modules\KctAdmin\Services\BusinessServices\IGroupService;
use Modules\KctAdmin\Services\BusinessServices\ISpaceService;
use Modules\KctAdmin\Services\BusinessServices\ICoreService;
use Modules\KctAdmin\Services\BusinessServices\IValidationService;
use Modules\KctAdmin\Services\BusinessServices\IZoomService;
use Modules\KctAdmin\Services\DataServices\IDataService;
use Modules\KctAdmin\Services\OtherModuleCommunication\ISuperAdminService;
use Modules\KctAdmin\Services\OtherModuleCommunication\IUserManagementService;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is module base service which will contain all the dependencies with injected already
 * Following types of dependencies will be injected here
 *
 * 1. Service Dependencies
 * 2. Business Service Dependencies
 * 3. Communication Service Dependencies
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class BaseController
 * @package Modules\KctAdmin\Http\Controllers
 */
class BaseService {

    //services
    public ICoreService $kctService;
    public IDataService $dataFactory;
    public IValidationService $validationService;
    public IGroupService $groupService;
    public IEmailService $emailService;
    public IEventService $eventService;

    // Communication
    public IFileService $fileService;
    public ISpaceService $spaceService;
    public IZoomService $zoomService;

    // other module services
    public IUserManagementService $userService;
    public ISuperAdminService $superAdminService;

    private ?BaseRepo $repo = null;
    public IColorExtractService $colorExtService;
    public ICoreService $coreService;
    public IAnalyticsService $analyticsService;

    public function __construct(
        //services
        IFileService           $fileService,
        ICoreService           $kctService,
        ISpaceService          $spaceService,
        IValidationService     $validationService,
        IGroupService          $groupService,
        IEmailService          $emailService,
        IAnalyticsService      $analyticsService,
        IEventService          $eventService,

        IDataService           $dataFactory,
        IColorExtractService   $colorExtService,
        ICoreService           $coreService,

        IUserManagementService $userManagementService,
        ISuperAdminService     $superAdminService,

        IZoomService           $zoomService
    ) {
        $this->fileService = $fileService;
        $this->kctService = $kctService;
        $this->spaceService = $spaceService;
        $this->validationService = $validationService;
        $this->groupService = $groupService;
        $this->colorExtService = $colorExtService;
        $this->coreService = $coreService;
        $this->dataFactory = $dataFactory;
        $this->userService = $userManagementService;
        $this->superAdminService = $superAdminService;
        $this->zoomService = $zoomService;
        $this->emailService = $emailService;
        $this->analyticsService = $analyticsService;
        $this->eventService = $eventService;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the singleton BaseRepo Object
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return BaseRepo
     */
    public function getRepo(): BaseRepo {
        if (!$this->repo) {
            $this->repo = app(BaseRepo::class);
        }
        return $this->repo;
    }

}
