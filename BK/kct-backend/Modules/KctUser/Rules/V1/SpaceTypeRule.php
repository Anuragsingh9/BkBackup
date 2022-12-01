<?php

namespace Modules\KctUser\Rules\V1;

use Illuminate\Contracts\Validation\Rule;
use Modules\KctUser\Services\KctCoreService;
use Modules\KctUser\Services\KctUserValidationService;

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

    /**
     * Create a new rule instance.
     *
     * @param $uuid
     * @param $source
     */
    public function __construct($uuid, $source) {
        if ($source == 'event') {
            $this->eventUuid = $uuid;
        } else {
            $space = KctUserValidationService::getInstance()->resolveSpace($uuid);
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
            // if ($value == config('cocktail.default.space_type_vip')) {
            //     $result = ValidationV2Service::getInstance()->isVipCreated($this->eventUuid, $this->spaceUuid);
            //     if ($result) {
            //         $this->msg = __('cocktail::message.one_vip_possible');
            //         return false;
            //     }
            // }
            if ($value == config('kctuser.default.space_type_duo')) {
                if(KctCoreService::getInstance()->findEventConferenceType($this->eventUuid)) {
                    $this->msg = __('kctuser::message.conference_cannot_have_duo');
                    return false;
                }
                $result = KctUserValidationService::getInstance()->isDuoCreated($this->eventUuid, $this->spaceUuid);
                if ($result) {
                    $this->msg = __('kctuser::message.one_duo_possible');
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
