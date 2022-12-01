<?php

namespace Modules\KctAdmin\Services\OtherModuleCommunication;

use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Modules\SuperAdmin\Entities\Setting;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This interface will manage the super admin services
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface ISuperAdminService
 * @package Modules\KctAdmin\Services\OtherModuleCommunication
 */
interface ISuperAdminService {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is used to check first is setting created or not if not create then create it
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $key
     * @return Setting|null
     */
    public function firstOrCreateSetting($key): ?Setting;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the scenery data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function getAllSceneryData();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will be used to get event scenery data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $assetId
     * @return mixed
     */
    public function getEventSceneryData($assetId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the user grid image
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function getUserGridImage();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the all super admins of the group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function getAllSuperAdmins();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To set the tenant
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $website
     * @return mixed
     */
    public function setTenant($website);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the current organisation details
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function getOrganisation();
}
