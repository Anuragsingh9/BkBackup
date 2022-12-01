<?php


namespace Modules\KctAdmin\Services\BusinessServices;


use Illuminate\Database\Eloquent\Builder;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This interface will contain all methods related to the event service based
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IEventService
 *
 * @package Modules\KctAdmin\Services\DataServices
 */
interface IEventService {
    public function createWaterFountainEvent();

    public function getWaterFountainEvent($enableCheck=true);
}
