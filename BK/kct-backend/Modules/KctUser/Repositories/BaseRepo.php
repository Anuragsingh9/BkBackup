<?php


namespace Modules\KctUser\Repositories;


class BaseRepo {
    public IBanUserRepository $banUserRepository;
    public IConversationRepository $convRepository;
    public IConversationUserRepository $convUserRepository;
    public IDummyConvRepository $dummyConvRepository;
    public IOrgTagUserRepository $orgTagUserRepository;
    public ISpaceUserRepository $spaceUserRepository;
    public IUserInvitesRepository $invitesRepository;
    public IUserTagsRepository $userTagsRepository;
    public IUserRepository $userRepository;
    public IEventRepository $eventRepository;
    public ISettingRepository $settingRepository;

    public function __construct(
        IBanUserRepository $banUserRepository,
        IConversationRepository $convRepository,
        IConversationUserRepository $convUserRepository,
        IDummyConvRepository $dummyConvRepository,
        IOrgTagUserRepository $orgTagUserRepository,
        ISpaceUserRepository $spaceUserRepository,
        IUserInvitesRepository $invitesRepository,
        IUserTagsRepository $userTagsRepository,
        IUserRepository $userRepository,
        IEventRepository $eventRepository,
        ISettingRepository $settingRepository
    ) {
        $this->banUserRepository = $banUserRepository;
        $this->convRepository = $convRepository;
        $this->convUserRepository = $convUserRepository;
        $this->dummyConvRepository = $dummyConvRepository;
        $this->orgTagUserRepository = $orgTagUserRepository;
        $this->spaceUserRepository = $spaceUserRepository;
        $this->invitesRepository = $invitesRepository;
        $this->userTagsRepository = $userTagsRepository;
        $this->userRepository = $userRepository;
        $this->eventRepository = $eventRepository;
        $this->settingRepository = $settingRepository;
    }
}
