<?php

namespace Modules\KctAdmin\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\KctAdmin\Repositories\IKctSpaceRepository;

class SpaceExistsRule implements Rule {
    /**
     * @var Model
     */
    private $kctSpaceRepository;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {
        //
        $this->kctSpaceRepository = app(IKctSpaceRepository::class);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        return (boolean)$this->kctSpaceRepository->findById($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return __('kctadmin::messages.invalid_space');
    }
}
