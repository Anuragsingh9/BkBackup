<?php


namespace Modules\SuperAdmin\Services\BusinessServices\factory;

use Carbon\Carbon;
use Exception;
use Hyn\Tenancy\Contracts\Website;
use Hyn\Tenancy\Models\Hostname;
use Illuminate\Support\Facades\Artisan;
use Modules\SuperAdmin\Entities\GroupUser;
use Modules\SuperAdmin\Services\BusinessServices\IAccountService;
use Modules\SuperAdmin\Traits\ServicesAndRepo;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will manage the account services
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class AccountService
 * @package Modules\SuperAdmin\Services\BusinessServices\factory
 */
class AccountService implements IAccountService {

    use ServicesAndRepo;

    /**
     * @inheritDoc
     */
    public function createAccount(
        string  $accountName, string $orgName, string $orgFname, string $orgLname, string $orgEmail,
        ?string $password = null
    ): ?Hostname {
        $fqdn = "$accountName." . env("APP_HOST");

        // creating the website and hostname for new account
        $website = $this->suRepo()->accountRepository->createWebsite();
        $hostname = $this->suRepo()->accountRepository->createHostname($fqdn, $website);

        // To create the organisation information
        $this->createOrganisation(
            $hostname, $orgName, $orgFname, $orgLname, $orgEmail,
            $password
        );

        $this->runMigrateArtisanCommands($website);

        // setting tenant hostname for allowing to get hostname, although after website create tenant auto set website
        $this->suServices()->tenantService->setTenantByHostname($hostname);
        $this->suServices()->adminService->createDefaultSettings();
        $this->runSeedArtisanCommands($website);


        $group = $this->suServices()->adminService->createGroup([
            'name'        => $orgName,
            'short_name'  => $orgName,
            'description' => null,
            'settings'    => [],
            'is_default'  => 1,
            'group_key'   => config('superadmin.constants.default_group_key'),
        ], 'super_group');
        // Prepare the account organisation data
        $userData = $this->prepareAccountOrgData($orgFname, $orgLname, $orgEmail, $password);

        $user = $this->suServices()->userManagement->createUser(
            $userData, $group->id, GroupUser::$role_Organiser
        );

        // running seeder again for post group data
        $this->runSeedArtisanCommands($website);

        $companyData = [
            'long_name'      => $orgName ?? null,
            'entity_type_id' => 1, // 1 for company, 2 for union
        ];

        $this->suServices()->userManagement->updateUserEntity($user->id, $companyData);
        $this->suServices()->adminService->createWaterFountainEvent();

        $this->importDummyUsers($website);

        return $hostname;
    }

    /**
     * @inheritDoc
     */
    public function prepareAccountOrgData(string $fname, string $lname, string $email, ?string $password = null): array {
        $roles = $this->suServices()->userManagement->getRoles();
        return [
            'fname'             => $fname,
            'lname'             => $lname,
            'email'             => $email,
            'password'          => $password ?: $email,
            'roles'             => [$roles['org_admin'], $roles['main_organiser']],
            'email_verified_at' => Carbon::now()->toDateTimeString(),
            'login_count'       => 1
        ];
    }

    /**
     * @inheritDoc
     */
    public function prepareUrlAccountSignin(string $fqdn): string {
        return env("HOST_TYPE") . $fqdn . route("um-signin", [], false);
    }

    /**
     * @inheritDoc
     */
    public function prepareUrl(string $type, array $data = []): ?string {
        $hostType = env("HOST_TYPE"); // getting the host type to append (https/http etc.)
        $frontAdminUrl = env("APP_ADMIN_FRONT"); // front side react url without sub-domain
        switch ($type) {
            case 'access':
                return $this->suServices()->userManagement->prepareUrl($type, $data);
            default:
                return null;
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Here Organisation related task will done for account creation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Hostname $hostname
     * @param string $orgName
     * @param string $orgFname
     * @param string $orgLname
     * @param string $orgEmail
     * @param string|null $password
     */
    private function createOrganisation(
        Hostname $hostname,
        string   $orgName,
        string   $orgFname,
        string   $orgLname,
        string   $orgEmail,
        ?string  $password = null) {
        $organisation = [
            'name_org'    => $orgName,
            'acronym'     => $orgName,
            'fname'       => $orgFname,
            'lname'       => $orgLname,
            'email'       => $orgEmail,
            'password'    => $password,
            'hostname_id' => $hostname->id,
        ];
        // storing user data as organiser of account
        $this->suRepo()->organisationRepository->create($organisation);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To run the artisan related commands for creating the migration for account
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Website $website
     */
    private function runMigrateArtisanCommands(Website $website) {
        Artisan::call('tenancy:migrate', ['--website_id' => $website->id]);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To import the dummy users
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @throws Exception;
     */
    private function importDummyUsers(Website $website) {
        Artisan::call('dummy-user:sync', ['--website_id' => $website->id]);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To run the artisan related commands for creating the seeder for account
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Website $website
     */
    private function runSeedArtisanCommands(Website $website) {
        Artisan::call('tenancy:db:seed', ['--website_id' => $website->id]);
    }
}
