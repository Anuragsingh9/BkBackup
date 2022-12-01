<?php


namespace Modules\KctAdmin\Repositories;


use Illuminate\Database\Eloquent\Collection;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will contain locale related repository methods
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface ISettingRepository
 * @package Modules\KctAdmin\Repositories
 */
interface ILabelRepository {

    /**
     * @param null $groupId
     * @return Collection
     * ---------------–---------------–---------------–---------------–---------------–---------------–---------------–-
     * @description To get all the labels of a group
     * ---------------–---------------–---------------–---------------–---------------–---------------–---------------–-
     */
    public function getAll($groupId = null): Collection;

    /**
     * ---------------–---------------–---------------–---------------–---------------–---------------–---------------–-
     * @description To get the labels model collection by name
     * ---------------–---------------–---------------–---------------–---------------–---------------–---------------–-
     *
     * @param $names
     * @param null $groupId
     * @return mixed
     */
    public function getLabelsByName($names, $groupId = null);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the specific locale value from label name
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string|null $name
     * @param string|null $locale
     * @param null $groupId
     * @return mixed
     */
    public function getLocaleByName(?string $name, ?string $locale, $groupId = null);
}
