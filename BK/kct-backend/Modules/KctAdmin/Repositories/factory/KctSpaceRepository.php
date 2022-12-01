<?php

namespace Modules\KctAdmin\Repositories\factory;

use Illuminate\Support\Arr;
use Modules\KctAdmin\Entities\Space;
use Modules\KctAdmin\Repositories\IKctSpaceRepository;
use Modules\KctUser\Entities\EventSpaceUser;
use Nwidart\Modules\Collection;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will manage the space management functionality
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class KctSpaceRepository
 * @package Modules\KctAdmin\Repositories\factory
 */
class KctSpaceRepository implements IKctSpaceRepository {

    /**
     * @inheritDoc
     */
    public function create($param): Space {
        $hosts = null;
        if (isset($param['hosts'])) {
            $hosts = $param['hosts'];
            unset($param['hosts']);
        }
        $space = Space::create($param);
        if ($hosts) {
            $space->hosts()->attach($hosts);
        }
        return $space;
    }

    /**
     * @inheritDoc
     */
    public function updateSpace($spaceUuid, $data) {
        $space = Space::find($spaceUuid);
        if ($space) {
            $space->space_name = Arr::exists($data, 'space_name') ? $data['space_name'] : $space->space_name;
            $space->space_short_name = Arr::exists($data, 'space_short_name') ? $data['space_short_name'] : $space->space_short_name;
            $space->max_capacity = Arr::exists($data, 'max_capacity') ? $data['max_capacity'] : $space->max_capacity;
            $space->is_vip_space = Arr::exists($data, 'is_vip_space') ? $data['is_vip_space'] : $space->is_vip_space;
            $space->update();
        }
        return $space;
    }

    /**
     * @inheritDoc
     */
    public function findSpaceByUuid($spaceUuid): ?Space {
        return Space::find($spaceUuid);
    }

    /**
     * @inheritDoc
     */
    public function getDefaultSpace($eventUuid) {
        return Space::where('event_uuid', $eventUuid)->orderBy('created_at', 'asc')->first();
    }

    public function shiftSpaceUserToDefaultSpace($deleteSpaces, $defaultSpace) {
        $users = new Collection();

        foreach ($deleteSpaces as $deleteSpace) {
            $users = $users->merge($deleteSpace->spaceUsers);
        }
        $defaultSpaceUsers = $defaultSpace->spaceUsers;
        foreach ($users as $user) {
            $flag = true;
            foreach ($defaultSpaceUsers as $du) {
                if ($du->user_id == $user->user_id) {
                    $flag = false;
                }
            }
            if ($flag) {
                EventSpaceUser::updateOrCreate([
                    'space_uuid' => $defaultSpace->space_uuid,
                    'user_id'    => $user->user_id,
                ], ['role' => 2]);
            }
        }

    }
}
