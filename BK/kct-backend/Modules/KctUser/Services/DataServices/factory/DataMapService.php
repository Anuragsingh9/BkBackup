<?php


namespace Modules\KctUser\Services\DataServices\factory;


use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Modules\KctUser\Services\DataServices\IDataMapService;
use Modules\KctUser\Traits\Services;

class DataMapService implements IDataMapService {
    use Services;


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To load the personal and professional tags for the single user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $user
     * @param null $allTags
     * @return mixed
     */
    public function loadPPTagsForUser($user, $allTags = null) {
        $allTags = $allTags ?: $this->userServices()->superAdminService->getAllTags();
        if ($user->is_dummy) {
            $user->professionalTags = collect([]);
            $user->personalTags = collect([]);
        } else {
            $usedTags = $user->tagsRelationForPP->pluck('tag_id');
            $user->professionalTags = $allTags->whereIn('id', $usedTags)->where("tag_type", 1)->values();
            $user->personalTags = $allTags->whereIn('id', $usedTags)->where("tag_type", 2)->values();


        }
        return $user;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To remove the spaces which are full
     * @warn if need to exclude the default space then spaces must sorted by created to assume first space as default.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Collection $spaceCollection
     * @param bool $excludeDefaultSpace
     * @return Collection
     */
    public function removeFullSpaces($spaceCollection, $excludeDefaultSpace = true) {
        $defaultSpace = null;
        if ($excludeDefaultSpace) {
            $defaultSpace = $spaceCollection->first()->space_uuid;
        }
        return $spaceCollection->filter(function ($space) use ($defaultSpace) {
            return $space->space_uuid == $defaultSpace
                || !$space->max_capacity
                || $space->spaceUsers->count() < $space->max_capacity;
        });
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To add the host data to the all space response for front
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return mixed
     */
    public function addSpaceHostData($event) {
        $currentSpaceHost = $event->currentSpace->hosts->pluck('id')->toArray();
        if (isset($event->currentSpace->singleUsers) && $event->currentSpace->singleUsers) {
            //if user is single in current space then add the space host key indicator to Collection of users
            $event->currentSpace->singleUsers =
                $this->addSpaceHostKeyToUsers($event->currentSpace->singleUsers, $currentSpaceHost);
        }
        if (isset($event->currentSpace->conversations) && $event->currentSpace->conversations) {
            //if user in conversation then add the space host key to all the conversations collection
            $event->currentSpace->conversations =
                $this->addSpaceHostKeyToConversations($event->currentSpace->conversations, $currentSpaceHost);
        }
        if (isset($event->currentSpace->currentConversation) && $event->currentSpace->currentConversation) {
            //if user in current conversation then add the space host key indicator to Collection of users
            $event->currentSpace->currentConversation->users =
                $this->addSpaceHostKeyToUsers($event->currentSpace->currentConversation->users, $currentSpaceHost);
        }
        return $event;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To add the space host key indicator to Collection of users
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Collection $users
     * @param array $hosts
     * @return Collection
     */
    public function addSpaceHostKeyToUsers($users, $hosts) {
        return $users->map(function ($user) use ($hosts) {
            if (in_array($user->id, $hosts)) {
                $user->is_space_host = true;
            }
            return $user;
        });
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To add the space host key to all the conversations collection
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Collection $conversations
     * @param array $hosts
     * @return Collection
     */
    public function addSpaceHostKeyToConversations($conversations, $hosts) {
        return $conversations->map(function ($conversation) use ($hosts) {
            $conversation->users = $this->addSpaceHostKeyToUsers($conversation->users, $hosts);
            return $conversation;
        });
    }

}
