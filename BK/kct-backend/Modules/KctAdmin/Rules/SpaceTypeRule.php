<?php

namespace Modules\KctAdmin\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\KctAdmin\Services\BusinessServices\IValidationService;
use Modules\KctAdmin\Services\BusinessServices\ICoreService;

class SpaceTypeRule implements Rule {
    /**
     * @var string
     */
    private $eventUuid;
    /**
     * @var string
     */
    private $msg;
    private $spaceUuid;
    private IValidationService $validationService;
    private ICoreService $kctService;

    /**
     * Create a new rule instance.
     *
     * @param $uuid
     * @param $source
     */
    public function __construct($uuid, $source) {
        $this->kctService = app(ICoreService::class);
        $this->validationService = app(IValidationService::class);

        if ($source == 'event') {
            $this->eventUuid = $uuid;
        } else {
            $space = $this->validationService->resolveSpace($uuid);
            if ($space) {
                $this->eventUuid = $space->event_uuid;
                $this->spaceUuid = $uuid;
            }
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to validate the space type
     * 1. For vip
     * 2. For DUO
     * 0. For Regular
     *
     * This will validate to have at most one duo and one vip only
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $attribute
     * @param mixed $value // space type
     * @return bool
     */
    public function passes($attribute, $value) {
        if ($this->eventUuid) {
            // if ($value == config('kctadmin.default.space_type_vip')) {
            //     $result = ValidationV2Service::getInstance()->isVipCreated($this->eventUuid, $this->spaceUuid);
            //     if ($result) {
            //         $this->msg = __('kctadmin::messages.one_vip_possible');
            //         return false;
            //     }
            // }
            if ($value == config('kctadmin.default.space_type_duo')) {
                if ($this->kctService->findEventConferenceType($this->eventUuid)) {
                    $this->msg = __('kctadmin::messages.conference_cannot_have_duo');
                    return false;
                }
                $result = $this->validationService->isDuoCreated($this->eventUuid, $this->spaceUuid);
                if ($result) {
                    $this->msg = __('kctadmin::messages.one_duo_possible');
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return $this->msg;
    }
}
