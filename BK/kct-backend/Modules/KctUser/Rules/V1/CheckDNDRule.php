<?php

namespace Modules\KctUser\Rules\V1;

use App\User;
use Illuminate\Contracts\Validation\Rule;
use Modules\KctUser\Services\KctUserAuthorizationService;

class CheckDNDRule implements Rule {
    /**
     * @var array|string|null
     */
    private $msg;
    private $spaceUuid;

    /**
     * Create a new rule instance.
     *
     * @param $spaceUuid
     */
    public function __construct($spaceUuid) {
        $this->spaceUuid = $spaceUuid;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param string $userId
     * @return bool
     */
    public function passes($attribute, $userId) {
        if (User::find($userId) && !KctUserAuthorizationService::getInstance()
                ->isUserStateAvailable($userId, null, $this->spaceUuid)) {
            $this->msg = __('kctuser::message.not_allowed_in_dnd');
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
        return $this->msg;
    }
}
