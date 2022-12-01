<?php


namespace Modules\SuperAdmin\Repositories;


class BaseRepo {
    public ISuOtpRepository $otpRepository;
    public IAccountRepository $accountRepository;
    public IOrganisationRepository $organisationRepository;
    public ITagRepository $tagRepository;
    public IUserRepository $userRepository;
    public ISuperAdminRepository $superAdminRepository;
    public ISceneryRepository $sceneryRepository;

    public function __construct(
        ISuOtpRepository $otpRepository,
        IAccountRepository $accountRepository,
        IOrganisationRepository $organisationRepository,
        ITagRepository $tagRepository,
        IUserRepository $userRepository,
        ISuperAdminRepository $superAdminRepository,
        ISceneryRepository $sceneryRepository
    ) {
        $this->otpRepository = $otpRepository;
        $this->accountRepository = $accountRepository;
        $this->organisationRepository = $organisationRepository;
        $this->tagRepository = $tagRepository;
        $this->userRepository = $userRepository;
        $this->superAdminRepository = $superAdminRepository;
        $this->sceneryRepository = $sceneryRepository;
    }
}
