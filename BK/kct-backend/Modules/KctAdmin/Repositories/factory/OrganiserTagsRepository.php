<?php

namespace Modules\KctAdmin\Repositories\factory;

use Illuminate\Database\Eloquent\Collection;
use Modules\KctAdmin\Entities\GroupTag;
use Modules\KctAdmin\Entities\OrganiserTag;
use Modules\KctAdmin\Repositories\IOrganiserTagsRepository;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will be managing the organiser tags management
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface OrganiserTagsRepository
 * @package Modules\KctAdmin\Repositories\factory
 */
class OrganiserTagsRepository implements IOrganiserTagsRepository {

    /**
     * @inheritDoc
     */
    public function getOrderByNameAsc(): Collection {
        return OrganiserTag::orderBy('name', 'asc')->get();
    }

    /**
     * @inheritDoc
     */
    public function getByGroupId(int $groupId, string $orderBy = null, string $order = 'desc'): Collection {
        $builder = OrganiserTag::whereHas('group', function ($q) use ($groupId) {
            $q->where('group_id', $groupId);
        });
        if ($orderBy) {
            return $builder->orderBy($orderBy, $order)->get();
        }
        return $builder->get();
    }

    /**
     * @inheritDoc
     */
    public function create(array $param, int $groupId): ?OrganiserTag {
        $tag = OrganiserTag::create($param);
        GroupTag::create([
            'group_id' => $groupId,
            'tag_id'   => $tag->id,
        ]);
        return $tag;
    }

    /**
     * @inheritDoc
     */
    public function findById($id): OrganiserTag {
        return OrganiserTag::find($id);
    }

    /**
     * @inheritDoc
     */
    public function deleteById($id): int {
        return OrganiserTag::where('id', $id)->delete();
    }

    /**
     * @inheritDoc
     */
    public function storeMultipleTags($param) {
        GroupTag::insert($param);
    }
}
