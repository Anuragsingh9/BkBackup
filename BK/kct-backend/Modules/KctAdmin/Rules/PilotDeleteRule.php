<?php

namespace Modules\KctAdmin\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\KctAdmin\Traits\ServicesAndRepo;

class PilotDeleteRule implements Rule {
    use ServicesAndRepo;

    private $msg;

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
        $isGroupPilot = $this->adminServices()->validationService->isGroupPilot($value);
        if ($isGroupPilot) {
            $this->msg = __('kctadmin::messages.cannot_delete_pilot');
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string {
        return $this->msg;
    }
}
