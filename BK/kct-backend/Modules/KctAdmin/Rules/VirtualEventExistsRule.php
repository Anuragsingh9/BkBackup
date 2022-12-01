<?php

namespace Modules\KctAdmin\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\KctAdmin\Repositories\IEventRepository;

class VirtualEventExistsRule implements Rule {

    private IEventRepository $eventRepository;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {
        $this->eventRepository = app(IEventRepository::class);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool {
        return (boolean)$this->eventRepository->findByEventUuid($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string {
        return $this->key = __('kctadmin::messages.invalid_event');
    }
}
