<?php

namespace Modules\Cocktail\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Modules\Cocktail\Entities\EventSpaceUser;
use Modules\Events\Entities\Event;

class NoEventConversationJoinedRule implements Rule {
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        return (bool)Event::whereDoesntHave('spaces', function ($q) {
            $q->whereHas('spaceUsers', function ($q) {
                $q->where("user_id", Auth::user()->id);
                $q->whereNotNull('current_conversation_uuid');
                $q->whereHas('conversation');
            });
        })->where('event_uuid', $value)->first();
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
