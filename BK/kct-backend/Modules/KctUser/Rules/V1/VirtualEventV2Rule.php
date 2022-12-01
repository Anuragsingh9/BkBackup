<?php

namespace Modules\KctUser\Rules\V1;

use Illuminate\Contracts\Validation\Rule;
use Modules\Events\Entities\Event;
use Modules\KctUser\Entities\KctConference;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description to validate the event belongs to scrum 2 , i.e. virtual non bluejeans event
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class VirtualEventV2Rule
 * @package Modules\Cocktail\Rules
 */
class VirtualEventV2Rule implements Rule {

    /**
     * @var array|string|null
     */
    private $msg;

    public function __construct() {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        $event = Event::where('event_uuid', $value)->where('type', config('events.event_type.virtual'))->first();
        $conference = KctConference::where('event_uuid', $value)->first();
        if ($event && isset($conference->conference_settings['event_uses_bluejeans_event'])) {
            if ($conference->conference_settings['event_uses_bluejeans_event'] != 0) {
                $this->msg = __('kctuser::message.event_must_not_bj');
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
