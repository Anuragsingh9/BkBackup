<?php


namespace Modules\UserManagement\Repositories\factory;


use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\UserManagement\Entities\Entity;
use Modules\UserManagement\Entities\EntityUser;
use Modules\UserManagement\Repositories\IEntityRepository;
use Modules\UserManagement\Traits\ServicesAndRepo;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will be managing the entity repository
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * class EntityRepository
 * @package Modules\UserManagement\Repositories\factory
 */
class EntityRepository implements IEntityRepository {
    use ServicesAndRepo;

    /**
     * @inheritDoc
     */
    public function create(array $data): ?Entity {
        $data['long_name'] = ucwords($data['long_name']);
        return Entity::updateOrCreate([
            'long_name'      => $data['long_name'],
            'entity_type_id' => $data['entity_type_id'],
        ], $data
        );
    }

    /**
     * @inheritDoc
     */
    public function findByName(string $name, ?int $type = null, bool $like = false, bool $filterAlreadyAttach = false)
    : Collection {
        return Entity::where(function ($q) use ($like, $name, $type, $filterAlreadyAttach) {
            if ($like) {
                $q->where("long_name", "like", "%$name%");
            } else {
                $q->where("long_name", "$name");
            }
            if ($type) {
                $q->where('entity_type_id', $type);
            }
            if ($filterAlreadyAttach) {
                $q->whereDoesntHave('entityUsersRelation', function ($q) {
                    $q->where('user_id', Auth::user()->id);
                });
            }
        })->get();
    }

    /**
     * @inheritDoc
     */
    public function findById(int $id): ?Entity {
        return Entity::find($id);
    }

    /**
     * @inheritDoc
     */
    public function attachUserToEntity(int $userId, int $entityId, int $replaceId = null, $position = null) {
        $entity = $this->findById($entityId);
        if ($entity->entity_type_id == 1) {
            $previousEntity = $this->getUserCompanyRelation($userId);
            $replaceId = $previousEntity ? $previousEntity->entity_id : null;
        }

        $isUserAlreadyBelong = EntityUser::where([
            'user_id'   => $userId,
            'entity_id' => $entityId,
        ])->first();

        if ($isUserAlreadyBelong && $isUserAlreadyBelong->id != $replaceId) {
            // user already belong to a entity with which trying to replace
            // so to avoid after replace duplicate entry removing first
            EntityUser::where([
                'user_id'   => $userId,
                'entity_id' => $entityId,
            ])->delete();
        }

        return EntityUser::updateOrCreate([
            'user_id'   => $userId,
            'entity_id' => $replaceId ?: $entityId,
        ], [
            'user_id'   => $userId,
            'entity_id' => $entityId,
            'position'  => $position,
        ]);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the user's current company
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $userId
     * @return mixed
     */
    public function getUserCompanyRelation(int $userId) {
        return EntityUser::where('user_id', $userId)->whereHas('entity', function ($q) {
            $q->where("entity_type_id", 1);
        })->first();
    }

    /**
     * @inheritDoc
     */
    public function deleteEntityUser(int $userId, int $entityId) {
        return EntityUser::where('user_id', $userId)->where('entity_id', $entityId)->delete();
    }
}
