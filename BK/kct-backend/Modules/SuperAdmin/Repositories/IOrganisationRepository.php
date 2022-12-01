<?php


namespace Modules\SuperAdmin\Repositories;


use Doctrine\DBAL\Platforms\Keywords\OracleKeywords;
use Modules\SuperAdmin\Entities\Organisation;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will contain Account Level Organisation Data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IOrganisationRepository
 * @package Modules\SuperAdmin\Repositories
 */
interface IOrganisationRepository {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To store the organisation basic details.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $data
     * @return Organisation
     */
    public function create($data): Organisation;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To the organisation data by email
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $email
     * @return Organisation|null
     */
    public function findByEmail(string $email): ?Organisation;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the organisation model by hostname id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return Organisation|null
     */
    public function findByHostnameId($hostnameId): ?Organisation;
}
