<?php

namespace Modules\KctAdmin\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\KctAdmin\Entities\OrganiserTag;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will be managing the organiser tag management
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IOrganiserTagsRepository
 * @package Modules\KctAdmin\Repositories
 */
interface IOrganiserTagsRepository {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description get all organiser tags in ascending order of name
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return Collection
     */
    public function getOrderByNameAsc(): Collection;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get tags for specific group only
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $groupId
     * @param string|null $orderBy
     * @param string $order
     * @return Collection
     */
    public function getByGroupId(int $groupId, string $orderBy = null, string $order = 'desc'): Collection;


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description create new organiser tag
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param array $param
     * @param int $groupId
     * @return OrganiserTag
     */
    public function create(array $param, int $groupId): ?OrganiserTag;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description get organiser tag by id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $id
     * @return OrganiserTag
     */
    public function findById($id): OrganiserTag;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description delete organiser tag by id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $id
     * @return int
     */
    public function deleteById($id): int;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To store multiple tags at once
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $param
     * @return mixed
     */
    public function storeMultipleTags($param);
}
