<?php


namespace Modules\UserManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\UserManagement\Entities\Entity;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will be managing the entity(company and union) repository
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IEntityRepository
 * @package Modules\UserManagement\Repositories
 */
interface IEntityRepository {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create entity
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param array $data
     * @return Entity|null
     */
    public function create(array $data): ?Entity;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find the entity by name
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $name
     * @param int|null $type
     * @param false $like
     * @param bool $filterAlreadyAttach
     * @return Collection
     */
    public function findByName(string $name, ?int $type = null, bool $like = false, bool $filterAlreadyAttach = false): Collection;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find the entity by id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $id
     * @return Entity|null
     */
    public function findById(int $id): ?Entity;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To add a user in entity by id or by name,
     * @note in by name if name is not present new entity will be created
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $userId
     * @param int $entityId
     * @param int|null $replaceId
     * @param null $position
     * @return mixed
     */
    public function attachUserToEntity(int $userId, int $entityId, int $replaceId = null, $position = null);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To remove an entity from user's profile
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $userId
     * @param int $entityId
     * @return mixed
     */
    public function deleteEntityUser(int $userId, int $entityId);


}
