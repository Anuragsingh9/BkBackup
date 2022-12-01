<?php


namespace Modules\KctAdmin\Services\BusinessServices;


use phpDocumentor\Reflection\Types\Mixed_;

interface ISpaceService {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To search for space host for an event space
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $val
     * @param $eventUuid
     * @return Users;
     */
    public function searchSpaceHost($eventUuid, $val);
}
