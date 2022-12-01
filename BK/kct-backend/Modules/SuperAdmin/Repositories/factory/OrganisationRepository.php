<?php


namespace Modules\SuperAdmin\Repositories\factory;


use Illuminate\Support\Facades\Hash;
use Modules\SuperAdmin\Entities\Organisation;
use Modules\SuperAdmin\Repositories\IOrganisationRepository;

/**
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will contain the account level organisation data
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class UpdateAccountSettings
 * @package Modules\SuperAdmin\Repositories\factory
 */
class OrganisationRepository implements IOrganisationRepository {

    /**
     * @inheritDoc
     */
    public function create($data): Organisation {
        return Organisation::create([
            'hostname_id' => $data['hostname_id'] ?? null,
            'name_org'    => ucwords($data['name_org'] ?? null),
            'acronym'     => $data['acronym'] ?? null,
            'fname'       => ucwords($data['fname'] ?? null),
            'lname'       => ucwords($data['lname'] ?? null),
            'email'       => $data['email'] ?? null,
            'password'    => Hash::make($data['password'] ?? $data['email']),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function findByEmail(string $email): ?Organisation {
        return Organisation::where("email", $email)->first();
    }

    /**
     * @inheritDoc
     */
    public function findByHostnameId($hostnameId): ?Organisation {
        return Organisation::where('hostname_id', $hostnameId)->first();
    }
}
