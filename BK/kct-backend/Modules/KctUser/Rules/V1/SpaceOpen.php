<?php

namespace Modules\KctUser\Rules\V1;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Modules\Events\Entities\Event;
use Modules\Events\Service\ValidationService;
use Modules\KctAdmin\Entities\Space;
use Modules\KctUser\Entities\EventSpace;
use Modules\KctUser\Services\KctCoreService;
use Modules\KctUser\Services\KctUserAuthorizationService;
use Modules\KctUser\Traits\Services;

class SpaceOpen implements Rule {
    private $key;
    use Services;

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool {
        $space = Space::with('event')->where('space_uuid', $value)->first();
        if ($space && $space->event) {
            if ($this->userServices()->kctService->eventCheckAccessCode($space->event, request()->input('access_code'))) {
                return true;
            }

            $start = Carbon::createFromFormat('Y-m-d H:i:s', $space->event->start_time)->timestamp;
            $end = Carbon::createFromFormat('Y-m-d H:i:s', $space->event->end_time)->timestamp;
            $current = Carbon::now()->timestamp;

            if ($start <= $current && $current < $end) {
                return true;
            } else {
                $this->key = __('cocktail::message.space_not_started');
                return false;
            }
        } else {
            $this->key = __('cocktail::message.invalid_space');
            $result = false;
        }
        return $result;
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
