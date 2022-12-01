<?php

namespace Modules\KctAdmin\Repositories;

use Modules\KctAdmin\Entities\Space;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will contain the space management methods
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IKctSpaceRepository
 * @package  Modules\KctAdmin\Repositories
 */
interface IKctSpaceRepository {

    /**
     * -----------------------------------------------------------------------------
     * @description To create an event space
     * -----------------------------------------------------------------------------
     *
     * @param $param
     * @return Space
     */
    public function create($param): Space;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update a space data for an event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $spaceUuid
     * @param $data
     * @return mixed
     */
    public function updateSpace($spaceUuid, $data);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the space by space uuid
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $spaceUuid
     * @return Space|null
     */
    public function findSpaceByUuid($spaceUuid): ?Space;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the default space of the event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @return mixed
     */
    public function getDefaultSpace($eventUuid);

    public function shiftSpaceUserToDefaultSpace($deleteSpaces, $defaultSpace);
}
