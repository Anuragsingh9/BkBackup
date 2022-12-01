<?php


namespace Modules\UserManagement\Services;


use Modules\UserManagement\Repositories\BaseRepository;
use Modules\UserManagement\Services\BusinessServices\IEmailService;
use Modules\UserManagement\Services\BusinessServices\IFileService;
use Modules\UserManagement\Services\BusinessServices\IKctService;
use Modules\UserManagement\Services\BusinessServices\ITenantService;
use Modules\UserManagement\Services\BusinessServices\IUserService;
use Modules\UserManagement\Services\OtherModuleCommunication\IKctAdminService;
use Modules\UserManagement\Services\OtherModuleCommunication\ISuperAdminService;

class BaseService {
    public IFileService $fileService;
    public ITenantService $tenantService;
    public IEmailService $emailService;
    public IKctService $kctService;
    public IUserService $userService;
    public IKctAdminService $adminService;
    public ISuperAdminService $superAdminService;

    public function __construct(
        IFileService $fileService,
        ITenantService $tenantService,
        IEmailService $emailService,
        IKctService $kctService,
        IUserService $userService,
        IKctAdminService $kctAdminService,
        ISuperAdminService $superAdminService
    ) {
        $this->fileService = $fileService;
        $this->tenantService = $tenantService;
        $this->emailService = $emailService;
        $this->kctService = $kctService;
        $this->userService = $userService;
        $this->adminService = $kctAdminService;
        $this->superAdminService = $superAdminService;
    }
}
