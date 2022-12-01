<?php


namespace Modules\SuperAdmin\Services\OtherModuleCommunication\factory;


use Exception;
use Modules\KctAdmin\Entities\Group;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\SuperAdmin\Services\OtherModuleCommunication\IKctAdminCommunication;

/**
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will manage the admin services
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class KctAdminService
 * @package Modules\SuperAdmin\Services\OtherModuleCommunication\factory
 */
class KctAdminService implements IKctAdminCommunication {
    use ServicesAndRepo;
    use \Modules\SuperAdmin\Traits\ServicesAndRepo;

    /**
     * @inheritDoc
     */
    public function createDefaultSettings(): void {
        $this->adminRepo()->settingRepository->storeDefaultSettings();
    }

    /**
     * @inheritDoc
     */
    public function getAccountSetting(): array {
        return $this->adminRepo()->settingRepository->getAccountSetting();
    }

    /**
     * @inheritDoc
     */
    public function getConferenceSetting(): array {
        return $this->adminRepo()->settingRepository->getConferenceSetting();
    }

    /**
     * @inheritDoc
     */
    public function updateAccountSetting($data): void {
        $this->adminRepo()->settingRepository->updateAccountSetting($data);
    }

    /**
     * @inheritDoc
     */
    public function updateConfSetting($data): void {
        $this->adminRepo()->settingRepository->updateConfSetting($data);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function createGroup(array $data,$groupType): Group {
        return $this->adminServices()->groupService->createGroup($data,$groupType);
    }

    /**
     * @inheritDoc
     */
    public function fetchSuperGroup($accountId){
        return $this->adminServices()->groupService->getSuperGroup($accountId);
    }

    /**
     * @inheritDoc
     */
    public function getGroupEvent() {
        return $this->adminRepo()->eventRepository->getEvents('future');
    }

    public function createWaterFountainEvent() {
        $this->adminServices()->eventService->createWaterFountainEvent();
    }
}
