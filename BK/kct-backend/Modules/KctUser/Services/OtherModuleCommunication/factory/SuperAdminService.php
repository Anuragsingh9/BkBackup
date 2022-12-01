<?php


namespace Modules\KctUser\Services\OtherModuleCommunication\factory;


use Modules\KctUser\Services\OtherModuleCommunication\ISuperAdminService;
use Modules\SuperAdmin\Entities\Setting;
use Modules\SuperAdmin\Repositories\ISettingRepository;
use Modules\SuperAdmin\Traits\ServicesAndRepo;
use Modules\UserManagement\Repositories\IKctSpaceRepository;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This repository is responsible for managing the super admin services
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class SuperAdminService
 * @package Modules\KctUser\Services\OtherModuleCommunication\factory
 */
class SuperAdminService implements ISuperAdminService {
    use ServicesAndRepo;


    /**
     * @inheritDoc
     */
    public function getUserTagByStatus($status) {
        return $this->userTagRepository->getUserTagByStatus($status);
    }

    /**
     * @inheritDoc
     */
    public function findTagByName(?string $tagName, ?string $tagType, ?string $lang) {
        return $this->suRepo()->tagRepository->findByName($tagName, $tagType, $lang);
    }

    /**
     * @inheritDoc
     */
    public function createTag($tagName, $tagType) {
        return $this->suRepo()->tagRepository->create($tagName, $tagType);
    }

    /**
     * @inheritDoc
     */
    public function getTagByKey($key, $locale, $tagType) {
        return $this->suRepo()->tagRepository->getTagByKey($key, $locale, $tagType);
    }

    /**
     * @inheritDoc
     */
    public function getGeneralSettings(): ?Setting {
        return $this->suRepo()->superAdminRepository->getSettingByKey('public_video');
    }

    /**
     * @inheritDoc
     */
    public function getTagById($id) {
        return $this->suRepo()->tagRepository->findById($id);
    }

    /**
     * @inheritDoc
     */
    public function deleteUserTagRelationByTagId($userId, $tagId) { // todo move to kct user
        return $this->userTagRelationRepository->deleteUserTagRelationByTagId($userId, $tagId);
    }

    /**
     * @inheritDoc
     */
    public function createUserTagRelation($userId, $tagId) { // todo move to kct user
        return $this->userTagRelationRepository->createUserTagRelation($userId, $tagId);
    }

    /**
     * @inheritDoc
     */
    public function getSettingByKey($key, $groupId) {
        //        $setting = Setting::where('setting_key', $key)->first();
        return $this->settingRepository->getSettingByKey($key, $groupId);
    }

    /**
     * @inheritDoc
     */
    public function getHostnameWithOrg() {
        return $this->hostnameModelRepository->getHostnameWithOrg();
    }

    /**
     * @inheritDoc
     */
    public function findHostname($accountId) {
        return $this->hostnameRepository->findHostname($accountId);
    }

    /**
     * @inheritDoc
     */
    public function getOtp($email) {
        return $this->signUpRepository->getOtp($email);
    }

    /**
     * @inheritDoc
     */
    public function updateCreateSetting($customGraphics, $defaultValues) { // todo json_encode($defaultValues)
        $this->settingRepository->updateCreateSetting($customGraphics, json_encode($defaultValues));
    }

    /**
     * @inheritDoc
     */
    public function getAccountSetting($hostnameId) {
        return $this->accountSettingsRepository->getAccountSetting($hostnameId);
    }

    /**
     * @inheritDoc
     */
    public function getAllTags($status = null) {
        return $this->suRepo()->tagRepository->getAll($status);
    }


}
