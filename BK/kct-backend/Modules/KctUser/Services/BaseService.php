<?php


namespace Modules\KctUser\Services;

use Modules\KctUser\Repositories\BaseRepo;
use Modules\KctUser\Services\BusinessServices\IApiService;
use Modules\KctUser\Services\BusinessServices\IAuthorizationService;
use Modules\KctUser\Services\BusinessServices\IEmailService;
use Modules\KctUser\Services\BusinessServices\IEventTimeService;
use Modules\KctUser\Services\BusinessServices\IFileService;
use Modules\KctUser\Services\BusinessServices\IKctService;
use Modules\KctUser\Services\BusinessServices\IKctUserEventService;
use Modules\KctUser\Services\BusinessServices\IKctUserService;
use Modules\KctUser\Services\BusinessServices\IKctUserSpaceService;
use Modules\KctUser\Services\BusinessServices\IKctUserValidationService;
use Modules\KctUser\Services\BusinessServices\IOrganiserService;
use Modules\KctUser\Services\BusinessServices\IRtcService;
use Modules\KctUser\Services\BusinessServices\IVideoChatService;
use Modules\KctUser\Services\DataServices\IDataMapService;
use Modules\KctUser\Services\DataServices\IDataService;
use Modules\KctUser\Services\OtherModuleCommunication\IKctAdminService;
use Modules\KctUser\Services\OtherModuleCommunication\ISuperAdminService;
use Modules\KctUser\Services\OtherModuleCommunication\IUserManagementService;

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
 * Class BaseService
 * @package Modules\KctUser\Services
 */
class BaseService {
    public IApiService $apiService;
    public IAuthorizationService $authorizationService;
    public IEmailService $emailService;
    public IEventTimeService $eventTimeService;
    public IKctService $kctService;
    public IKctUserEventService $eventService;
    public IKctUserService $userService;
    public IKctUserSpaceService $spaceService;
    public IKctUserValidationService $validationService;
    public IOrganiserService $organiserService;
    public IRtcService $rtcService;
    public IVideoChatService $videoChatService;
    public IFileService $fileService;

    public IDataMapService $dataMapServices;
    public IDataService $dataService;

    public IKctAdminService $adminService;
    public ISuperAdminService $superAdminService;
    public IUserManagementService $userManagementService;
    public HelperService $helperService;
    private ?BaseRepo $repo = null;

    public function __construct(
        IApiService $apiService,
        IAuthorizationService $authorizationService,
        IEmailService $emailService,
        IEventTimeService $eventTimeService,
        IKctService $kctService,
        IKctUserEventService $eventService,
        IKctUserService $userService,
        IKctUserSpaceService $spaceService,
        IKctUserValidationService $validationService,
        IOrganiserService $organiserService,
        IRtcService $rtcService,
        IVideoChatService $videoChatService,
        IFileService $fileService,

        HelperService $helperService,

        IDataMapService $dataMapServices,
        IDataService $dataService,

        IKctAdminService $adminService,
        ISuperAdminService $superAdminService,
        IUserManagementService $userManagementService
    ) {
        $this->apiService = $apiService;
        $this->authorizationService = $authorizationService;
        $this->emailService = $emailService;
        $this->eventTimeService = $eventTimeService;
        $this->kctService = $kctService;
        $this->eventService = $eventService;
        $this->userService = $userService;
        $this->spaceService = $spaceService;
        $this->validationService = $validationService;
        $this->organiserService = $organiserService;
        $this->rtcService = $rtcService;
        $this->videoChatService = $videoChatService;
        $this->fileService = $fileService;

        $this->helperService = $helperService;

        $this->dataMapServices = $dataMapServices;
        $this->dataService = $dataService;

        $this->adminService = $adminService;
        $this->superAdminService = $superAdminService;
        $this->userManagementService = $userManagementService;
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
