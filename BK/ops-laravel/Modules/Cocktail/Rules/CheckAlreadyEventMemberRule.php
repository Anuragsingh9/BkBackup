<?php

namespace Modules\Cocktail\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Events\Entities\Event;

class CheckAlreadyEventMemberRule implements Rule {
    private $column;
    private $userId;
    
    /**
     * Create a new rule instance.
     *
     * @param $column
     */
    public function __construct($column, $data) {
        $this->column = $column;
        $data = json_decode($data, 1);
        $this->userId = isset($data['id']) && $data['id'] ? $data['id'] : null;
    }
    
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param $eventColumnValue
     * @return bool
     */
    public function passes($attribute, $eventColumnValue) {
        if ($this->userId) {
            $event = Event::whereHas('eventUserRelation', function ($q) {
                $q->where('user_id', $this->userId);
            })->where($this->column, $eventColumnValue)->first();
            return !$event;
        }
        return true;
    }
    
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return __('cocktail::message.already_event_member');
    }
}
