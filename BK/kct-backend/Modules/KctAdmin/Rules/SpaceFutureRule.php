<?php

namespace Modules\KctAdmin\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\KctAdmin\Services\BusinessServices\IValidationService;
use Modules\KctAdmin\Traits\ServicesAndRepo;

class SpaceFutureRule implements Rule {
    use ServicesAndRepo;
    /**
     * @var array|string|null
     */
    private $key;
    /**
     * @var IValidationService
     */
    private IValidationService $validationService;
    private $space_uuid;
    private $space_type;

    /**
     * SpaceFutureRule constructor.
     * @param $spaceUuid
     * @param $spaceType
     */
    public function __construct($spaceUuid,$spaceType) {
        //
        $this->space_uuid = $spaceUuid;
        $this->space_type = $spaceType;
        $this->validationService = app(IValidationService::class);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        $space = $this->adminRepo()->kctSpaceRepository->findSpaceByUuid($this->space_uuid);
        $defaultSpace = $this->adminRepo()->kctSpaceRepository->getDefaultSpace($space->event_uuid);
        if (!$this->validationService->isSpaceFuture($value)) {
            $this->key = __('kctadmin::messages.event_must_future');
            return false;
        }if ($value == $defaultSpace->space_uuid){
            if ($this->space_type == 1){
                $this->key = __('kctadmin::messages.cannot_update_default_space_type');
                return false;
            }
            return true;
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
