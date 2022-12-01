<?php


namespace Modules\SuperAdmin\Services;


use Modules\SuperAdmin\Services\BusinessServices\IAccountService;
use Modules\SuperAdmin\Services\BusinessServices\IEmailService;
use Modules\SuperAdmin\Services\BusinessServices\IFileService;
use Modules\SuperAdmin\Services\BusinessServices\ITenantService;
use Modules\SuperAdmin\Services\DataServices\IExportService;
use Modules\SuperAdmin\Services\DataServices\ITempDataService;
use Modules\SuperAdmin\Services\OtherModuleCommunication\IKctAdminCommunication;
use Modules\SuperAdmin\Services\OtherModuleCommunication\IUserManagement;

class BaseService {

    // Business Services
    public IAccountService $accountService;
    public IEmailService $emailService;
    public IFileService $fileService;
    public ITenantService $tenantService;
    // Data Services
    public IExportService $exportService;
    public ITempDataService $tempDataService;
    // Other module communication Services
    public IKctAdminCommunication $adminService;
    public IUserManagement $userManagement;

    public function __construct(
        IAccountService        $accountService,
        IEmailService          $emailService,
        IFileService           $fileService,
        ITenantService         $tenantService,
        IExportService         $exportService,
        ITempDataService       $tempDataService,
        IKctAdminCommunication $adminService,
        IUserManagement        $userManagement
    ) {
        $this->accountService = $accountService;
        $this->emailService = $emailService;
        $this->fileService = $fileService;
        $this->tenantService = $tenantService;
        $this->exportService = $exportService;
        $this->tempDataService = $tempDataService;
        $this->adminService = $adminService;
        $this->userManagement = $userManagement;
    }

}
