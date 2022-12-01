<?php

namespace Modules\Cocktail\Rules\V2;

use Illuminate\Contracts\Validation\Rule;
use Modules\Cocktail\Services\V2Services\ValidationV2Service;

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
        if(!ValidationV2Service::getInstance()->isSpaceFuture($value)) {
            $this->key = __('cocktail::message.event_must_future');
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
