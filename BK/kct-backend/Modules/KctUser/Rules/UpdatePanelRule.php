<?php

namespace Modules\KctUser\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Modules\KctUser\Traits\Services;

class UpdatePanelRule implements Rule {
    use Services;

    /**
     * @var array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    private $error;

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
    public function passes($attribute, $value): bool {
        $createdByUserId = $this->userServices()->validationService->getEventCreateByUserId($value);
        if (Auth::user()->id != $createdByUserId) {
            $this->error = __('validation.exists', ['attribute' => 'user']);
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
        return $this->error;
    }
}
