<?php


namespace Modules\SuperAdmin\Services\DataServices;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This service will provide the solution to store the temporary data.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface ITempDataService
 * @package Modules\SuperAdmin\Services\DataServices
 */
interface ITempDataService {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To store the data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param array $data
     */
    public function put(array $data): void;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $key
     * @return string|null
     */
    public function get(string $key): ?string;

}
