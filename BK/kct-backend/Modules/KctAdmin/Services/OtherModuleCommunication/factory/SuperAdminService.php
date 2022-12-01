<?php


namespace Modules\KctAdmin\Services\OtherModuleCommunication\factory;


use Modules\KctAdmin\Services\OtherModuleCommunication\ISuperAdminService;
use Modules\SuperAdmin\Entities\Setting;
use Modules\SuperAdmin\Traits\ServicesAndRepo;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will manage the super admin services
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class SuperAdminService
 * @package Modules\KctAdmin\Services\OtherModuleCommunication\factory
 */
class SuperAdminService implements ISuperAdminService {
    use ServicesAndRepo;

    /**
     * @inheritDoc
     */
    public function firstOrCreateSetting($key): ?Setting {
        return Setting::firstOrCreate(['setting_key' => $key], ['setting_value' => []]);
    }

    /**
     * @inheritDoc
     */
    public function getAllSceneryData() {
        return $this->suRepo()->sceneryRepository->fetchAllSceneryData();
    }

    /**
     * @inheritDoc
     */
    public function getEventSceneryData($assetId) {
        return $this->suRepo()->sceneryRepository->fetchEventSceneryData($assetId);
    }

    /**
     * @inheritDoc
     */
    public function getUserGridImage() {
        $setting = $this->suRepo()->superAdminRepository->getSettingByKey('public_video');
        $imagePath = $setting->setting_value['image_path'] ?? null;
        return $imagePath;

    }

    /**
     * @inheritDoc
     */
    public function getAllSuperAdmins() {
        return $this->suRepo()->superAdminRepository->getAllSuperAdmins();
    }

    /**
     * @inheritDoc
     */
    public function setTenant($website) {
        return $this->suServices()->tenantService->setTenantByWebsite($website);
    }

    /**
     * @inheritDoc
     */
    public function getOrganisation() {
        return $this->suRepo()->organisationRepository->findByHostnameId($this->suServices()->tenantService->getHostname()->id);
    }
}
