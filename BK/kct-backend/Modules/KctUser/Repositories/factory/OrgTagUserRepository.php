<?php


namespace Modules\KctUser\Repositories\factory;


use App\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\KctAdmin\Entities\GroupTag;
use Modules\KctUser\Entities\OrganiserTagUser;
use Modules\KctUser\Entities\OrganiserTag;
use Modules\KctUser\Repositories\IOrgTagUserRepository;

/**
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will be used for organiser tag repository
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class OrgTagUserRepository
 *
 * @package Modules\KctUser\Repositories\factory
 */
class OrgTagUserRepository implements IOrgTagUserRepository {

    /**
     * @inheritDoc
     */
    public function findUserTagByTagIdAndUserId($tagId, $userId) {
        return OrganiserTagUser::where(['tag_id' => $tagId, 'user_id' => $userId])->first(); // todo table name change
    }

    /**
     * @inheritDoc
     */
    public function create($tagId, $userId) {
        return OrganiserTagUser::create(['tag_id' => $tagId, 'user_id' => $userId]);
    }

    /**
     * @inheritDoc
     */
    public function delete($tagId, $userId) {
        return EventTagMata::where(['tag_id' => $tagId, 'user_id' => $tagId])->delete();
    }

    /**
     * @inheritDoc
     */
    public function getExistingTag(?User $user, $used = true, $eventUuid = null) {
        if ($used) {
            $result = OrganiserTag::whereExists(function ($query) use ($user) {
                $query->select(DB::raw(1))
                    ->from('organiser_tag_users')
                    ->whereColumn('organiser_tag_users.tag_id', 'organiser_tags.id')
                    ->where('organiser_tag_users.user_id', $user->id);
            })->where('is_display', 1)
                ->where(function ($query) use ($eventUuid) {
                    $query->whereHas('group', function ($query) use ($eventUuid) {
                        $query->whereHas('events', function ($query) use ($eventUuid) {
                            $query->where('events.event_uuid', $eventUuid);
                        });
                    });
                })
                ->orderBy('name', 'asc')->get(['id', 'name']);
        } else {
            $result = OrganiserTag::whereNotExists(function ($query) use ($user) {
                $query->select(DB::raw(1))
                    ->from('organiser_tag_users')
                    ->whereColumn('organiser_tag_users.tag_id', 'organiser_tags.id')
                    ->where('organiser_tag_users.user_id', $user->id);
            })->where('is_display', 1)
                ->where(function ($query) use ($eventUuid) {
                    $query->whereHas('group', function ($query) use ($eventUuid) {
                        $query->whereHas('events', function ($query) use ($eventUuid) {
                            $query->where('events.event_uuid', $eventUuid);
                        });
                    });
                })
                ->orderBy('name', 'asc')->get(['id', 'name']);
        }
        return $result;
    }
}
