<?php

namespace Modules\Cocktail\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Modules\Cocktail\Entities\EventSpaceUser;

class NoConversationJoinedRule implements Rule {
    protected $spaceUuid;
    
    /**
     * Create a new rule instance.
     *
     * @param $spaceUuid
     */
    public function __construct($spaceUuid) {
        $this->spaceUuid = $spaceUuid;
    }
    
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        $spaceUser = EventSpaceUser::where('user_id', Auth::user()->id)->where('space_uuid', $this->spaceUuid)->first();
        if ($spaceUser) {
            return !(boolean)$spaceUser->current_conversation_uuid;
        }
        return true; // if here means auth user is not space user
    }
    
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return __('cocktail::message.leave_conversation_first');
    }
}
