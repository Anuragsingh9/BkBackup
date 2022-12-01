<?php


namespace Modules\KctAdmin\Repositories;


use Modules\UserManagement\Repositories\IUserRepository;

class BaseRepo {
    // Repositories
    public IEventRepository $eventRepository;
    public IOrganiserTagsRepository $orgTagsRepository;
    public ISettingRepository $settingRepository;
    public IKctSpaceRepository $kctSpaceRepository;
    public IGroupRepository $groupRepository;
    public IGroupUserRepository $groupUserRepository;
    public IMomentRepository $momentRepository;
    public ILabelRepository $labelRepository;

    public function __construct(
        IEventRepository         $eventRepository,
        IOrganiserTagsRepository $orgTagsRepository,
        ISettingRepository       $settingRepository,
        IKctSpaceRepository      $kctSpaceRepository,
        IGroupRepository         $groupRepository,
        IGroupUserRepository     $groupUserRepository,
        IMomentRepository        $momentRepository,
        ILabelRepository         $labelRepository
    ) {
        $this->eventRepository = $eventRepository;
        $this->orgTagsRepository = $orgTagsRepository;
        $this->settingRepository = $settingRepository;
        $this->kctSpaceRepository = $kctSpaceRepository;
        $this->groupRepository = $groupRepository;
        $this->groupUserRepository = $groupUserRepository;
        $this->momentRepository = $momentRepository;
        $this->labelRepository = $labelRepository;
    }
}
