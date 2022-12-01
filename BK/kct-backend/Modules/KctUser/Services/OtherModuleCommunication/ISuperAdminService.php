<?php

namespace Modules\KctUser\Services\OtherModuleCommunication;

use Modules\SuperAdmin\Entities\Setting;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will be managing the super admin repositories
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface ISuperAdminService
 * @package Modules\KctUser\Services\OtherModuleCommunication
 */
interface ISuperAdminService {

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton To get user tag by the status
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param $status
     * @return mixed
     */
    public function getUserTagByStatus($status);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is use for find the tag by name
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string|null $tagName
     * @param string|null $tagType
     * @param string|null $lang
     * @return mixed
     */
    public function findTagByName(?string $tagName, ?string $tagType, ?string $lang);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for creating the user tags
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string|null $tagName
     * @param string|null $tagType
     * @return mixed
     */
    public function createTag(?string $tagName, ?string $tagType);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method use for get tag by key
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $key
     * @param $locale
     * @param $tagType
     * @return mixed
     */
    public function getTagByKey($key, $locale, $tagType);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method use for get general settings
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return Setting|null
     */
    public function getGeneralSettings(): ?Setting;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method used for get tag by id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $id
     * @return mixed
     */
    public function getTagById($id);

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton To delete the user tag relation by tag id
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId
     * @param $tagId
     * @return mixed
     */
    public function deleteUserTagRelationByTagId($userId, $tagId);

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton Create user tag relation
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId
     * @param $tagId
     * @return mixed
     */
    public function createUserTagRelation($userId, $tagId);

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the settings by key
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param $key
     * @param $groupId
     * @return mixed
     */
    public function getSettingByKey($key, $groupId);

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the host name with orgniser
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function getHostnameWithOrg();

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton Find the host name
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param $accountId
     * @return mixed
     */
    public function findHostname($accountId);

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the otp
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param $email
     * @return mixed
     */
    public function getOtp($email);

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton To update the create setting
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param $customGraphics
     * @param $defaultValues
     * @return mixed
     */
    public function updateCreateSetting($customGraphics, $defaultValues);

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the account setting
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param $hostnameId
     * @return mixed
     */
    public function getAccountSetting($hostnameId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is use for get all tags
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param null $status
     * @return mixed
     */
    public function getAllTags($status = null);

}
