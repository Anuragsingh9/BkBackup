<?php


namespace Modules\SuperAdmin\Services\BusinessServices;

use Exception;
use Hyn\Tenancy\Models\Hostname;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will contain the super admin account services
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IAccountService
 * @package Modules\SuperAdmin\Services\BusinessServices
 */
interface IAccountService {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create a account with predefined parameters
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $accountName
     * @param string $orgName
     * @param string $orgFname
     * @param string $orgLname
     * @param string $orgEmail
     * @param string|null $password
     * @return mixed
     * @throws Exception
     */
    public function createAccount(
        string $accountName, string $orgName, string $orgFname, string $orgLname, string $orgEmail, ?string $password = null
    ): ?Hostname;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create a user in account
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $fname
     * @param string $lname
     * @param string $email
     * @param string|null $password
     * @return array
     */
    public function prepareAccountOrgData(string $fname, string $lname, string $email, ?string $password = null): array;


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the url for the signin into an account
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $fqdn
     * @return string
     */
    public function prepareUrlAccountSignin(string $fqdn): string;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the urls for the different types
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $type
     * @param array $data
     * @return string|null
     */
    public function prepareUrl(string $type, array $data=[]): ?string;
}
