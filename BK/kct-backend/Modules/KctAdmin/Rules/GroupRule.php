<?php

namespace Modules\KctAdmin\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\KctAdmin\Traits\ServicesAndRepo;

class GroupRule implements Rule {
    use ServicesAndRepo;

    private string $attribute;
    /**
     * @var mixed
     */
    private $value;
    private string $error = '';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool {
        $this->attribute = $attribute;
        $this->value = $value;

        if (!$value) {
            return true;
        }

        switch ($attribute) {
            case "group_id":
                return $this->groupIdValidation();
            case "groupKey":
                return $this->groupKeyValidation();
            default:
                return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string {
        return $this->error;
    }

    private function groupIdValidation(): bool {
        $check = $this->adminRepo()->groupRepository->findById($this->value);
        if (!$check) {
            $this->error = __("validation.exists", ['attribute' => $this->attribute]);
            return false;
        }
        return true;
    }

    private function groupKeyValidation() : bool{
        $keyFound = $this->adminRepo()->groupRepository->findByGroupKey($this->value);
        if (!$keyFound) {
            $this->error = __("validation.exists", ['attribute' => $this->attribute]);
            return false;
        }
        return true;
    }
}
