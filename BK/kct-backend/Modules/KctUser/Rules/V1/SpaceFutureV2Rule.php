<?php

namespace Modules\KctUser\Rules\V1;

use Illuminate\Contracts\Validation\Rule;
use Modules\KctUser\Services\KctUserValidationService;

class SpaceFutureV2Rule implements Rule {
    /**
     * @var array|string|null
     */
    private $key;

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
        if(!KctUserValidationService::getInstance()->isSpaceFuture($value)) {
            $this->key = __('kctuser::message.event_must_future');
            return false;
        }
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
