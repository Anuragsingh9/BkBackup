<?php


namespace Modules\SuperAdmin\Repositories;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will contain scenery functions
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface ISceneryRepository
 * @package Modules\SuperAdmin\Repositories
 */
interface ISceneryRepository {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton this method fetch the all scenery data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return Builder[]|Collection
     */
    public function fetchAllSceneryData();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton this method fetch the event specific scenery data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $assetId
     * @return mixed
     */
    public function fetchEventSceneryData($assetId);
    }
