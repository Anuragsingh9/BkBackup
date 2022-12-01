<?php

namespace Modules\Messenger\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Messenger\Entities\WorkshopTopic;

class UniqueTopicName implements Rule {
    protected $topicId;
    
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($topicId) {
        $this->topicId = $topicId;
    }
    
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        if ($this->topicId) {
            if ($topic = WorkshopTopic::find($this->topicId)) {
                return !((boolean)WorkshopTopic::where('workshop_id', $topic->workshop_id)
                    ->where('topic_name', $value)
                    ->where('id', '!=', $this->topicId)
                    ->count());
            }
        }
        return TRUE;
    }
    
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return __('validation.unique');
    }
}
