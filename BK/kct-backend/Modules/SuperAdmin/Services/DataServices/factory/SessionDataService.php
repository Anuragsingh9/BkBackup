<?php


namespace Modules\SuperAdmin\Services\DataServices\factory;


use Illuminate\Support\Facades\Session;
use Modules\SuperAdmin\Services\DataServices\ITempDataService;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will provide storing data within session
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class SessionDataService
 * @package Modules\SuperAdmin\Services\DataServices\factory
 */
class SessionDataService implements ITempDataService {

    /**
     * @inheritDoc
     */
    public function get(string $key): ?string {
        return Session::get($key);
    }

    /**
     * @inheritDoc
     */
    public function put(array $data): void {
        Session::put($data);
    }
}
