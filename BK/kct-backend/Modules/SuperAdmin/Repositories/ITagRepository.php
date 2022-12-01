<?php


namespace Modules\SuperAdmin\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\SuperAdmin\Entities\UserTag;
use Modules\SuperAdmin\Entities\UserTagLocale;
use Modules\SuperAdmin\Exceptions\SuCustomException;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will contain tags management
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface ITagRepository
 * @package Modules\SuperAdmin\Repositories
 */
interface ITagRepository {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the un-moderated tags by tag type,
     * Pagination also supported
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $tagType
     * @param int|null $paginate
     */
    public function getUnModeratedTagByType(int $tagType, int $paginate = null);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the locale value of particular tag
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $tagId
     * @param string $tagValue
     * @param string $locale
     * @return UserTagLocale
     * @throws SuCustomException
     */
    public function updateTagLocaleValue(int $tagId, string $tagValue, string $locale): UserTagLocale;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the moderated tags by type
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $tagType
     * @param string|null $orderBy
     * @return mixed
     */
    public function getModeratedTagsByType(int $tagType, string $orderBy = null): Collection;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the tag status by tag id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $tagId
     * @param int $status
     * @return mixed
     */
    public function updateTagStatus(int $tagId, int $status): int;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To add the search tag functionality
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $key
     * @param string $tagType
     * @return Collection
     */
    public function searchTag(string $key, string $tagType): Collection;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find a tag by name
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $name
     * @param string $tagType
     * @param string|null $locale
     * @return UserTag
     */
    public function findByName(string $name, string $tagType, ?string $locale = null): ?UserTag;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find a tag by id
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string|null $id
     * @return UserTag
     */
    public function findById(?string $id): ?UserTag;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to get the tags by type key and locale
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string|null $key
     * @param string|null $locale
     * @param string|null $tagType
     * @return Collection
     */
    public function getTagByKey(?string $key, ?string $locale, ?string $tagType): Collection;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create a tag
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string|null $tagName
     * @param string|null $tagType
     * @return mixed
     */
    public function create(?string $tagName, ?string $tagType);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get all the tags
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param null $status
     * @return Collection
     */
    public function getAll($status = null): Collection;
}
