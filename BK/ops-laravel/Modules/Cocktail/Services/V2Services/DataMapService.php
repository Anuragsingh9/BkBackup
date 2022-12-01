<?php

namespace Modules\Cocktail\Services\V2Services;

use App\Services\Service;
use Illuminate\Database\Eloquent\Collection;


class DataMapService extends Service {
    
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
     * @description To load the tags for the user collection
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userCollection
     * @param $allTags
     * @return mixed
     */
    public function loadPPTagsForUserCollection($userCollection, $allTags) {
        return $userCollection->map(function($user) use($allTags) {
           return $this->loadPPTagsForUser($user, $allTags);
        });
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To load the personal and professional tags for the single user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $user
     * @param $allTags
     * @return mixed
     */
    public function loadPPTagsForUser($user, $allTags) {
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
}
