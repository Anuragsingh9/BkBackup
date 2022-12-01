<?php

namespace Modules\KctUser\Rules\V1;

use Illuminate\Contracts\Validation\Rule;
use Modules\KctAdmin\Entities\Space;
use Modules\KctUser\Entities\EventSpace;
use Illuminate\Support\Facades\Auth;

/**
 * - To validate the space have already max limit users or not
 *
 * @warn please handle the space not found as its not validating that,
 * and do not handle here else somewhere double message will shown for space not found
 *
 * Class SpaceMaxCountRule
 * @package Modules\Cocktail\Rules
 */
class SpaceMaxCountRule implements Rule {
    /**
     * @var string|null
     */
    private $msg;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param $spaceUuid
     * @return bool
     */
    public function passes($attribute, $spaceUuid) {
        $space = Space::with(['spaceUsers' => function ($q) {
            $q->where('user_id', '!=', Auth::user()->id);
        }])->where('space_uuid', $spaceUuid)->first();

        if ($space) { // if space not found other validation handling
            if ($space->max_capacity && $space->spaceUsers->count() >= $space->max_capacity) {
                $this->msg = __('kctuser::message.space_full');
                return false;
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return $this->msg;
    }
}
