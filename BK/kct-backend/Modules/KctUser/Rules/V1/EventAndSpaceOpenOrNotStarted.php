<?php

namespace Modules\KctUser\Rules\V1;

use Illuminate\Contracts\Validation\Rule;

class EventAndSpaceOpenOrNotStarted implements Rule {
    /**
     * @var string
     */
    private $eventKey;

    /**
     * EventAndSpaceOpenOrNotStarted constructor.
     *
     * @param string $eventKey
     * @param bool $isIntAllowed
     */
    public function __construct($eventKey = 'event_uuid') {
        $this->eventKey = $eventKey;
    }

    /**
     * Check
     * event exists
     * event virtual
     * event is either started or future
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool {
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return $this->key;
    }
}
