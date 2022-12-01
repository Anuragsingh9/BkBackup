<?php

namespace Modules\KctUser\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctUser\Traits\Repo;
use Modules\KctUser\Traits\Services;

class EventRegisterRule implements Rule {
    use Repo, Services;
    use KctHelper;

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
     * -----------------------------------------------------------------------------------------------------------------
     * @description here the event join will be checked if user is allowed to enter in event or not
     *
     * Conditions are checked with respect to priority here
     * 1. Past event should be not accessible in any case
     * 2. Event must be published
     * 3. Access code must be valid if present, and event is future and published
     * 4. Registration must be open if not past, and published and not in rehearsal mode
     * 5. Registration time must be valid with respect to current time when event is future , published and reg are open
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        $event = $this->userRepo()->eventRepository->findByEventUuid($value);
        $pastEvent = $this->isPastEvent($event);
        $event = $event->load(['draft']);

        $email = request()->user('api') ? request()->user('api')->email :  request()->email ;

        if(!$email) {
            $this->error = __('validations.exists', ['attribute' => 'user']);
            return false;
        }

        $user = $this->userRepo()->userRepository->getUserByEmail($email );
        if ($user) {
            $userId = $user ? $user->id : Auth::user()->id;
            $isRegistered = $this->userServices()->eventService->hasUserRegisteredEvent($value, $userId);

        }
        if ($pastEvent) {
            // Handling past event
            if (!$isRegistered) { // checking if user has registered in event or not
                $this->error = __("kctuser::message.cannot_reg_past_event");
                return false;
            }
            return true;
        } else {
            // Handling future and live event
            if (request()->has('access_code') && request()->access_code) { // checking if request has access code
                if (!$this->userServices()->kctService->eventCheckAccessCode(
                    $event, request()->input('access_code'))
                ) {
                    $this->error = __("kctuser::message.invalid_access_code");
                    return false;
                }
            } else {
                if ($event->draft && $event->draft->is_reg_open === 0) { // Checking if registration is closed
                    return $this->returnError();
                }
            }
            return true;
        }

//        if ($pastEvent) { // checking if event is past or not
//            $this->error = __("kctuser::message.cannot_login_past_event", [
//                'attribute' => $attribute,
//            ]);
//            return false;
//        } else if ($event->draft && $event->draft->event_status == 2) { // 2 = draft
//            // as event is not published so not allowing on any condition to log in to this event
//            return $this->returnError();
//        } else if ($this->userServices()->kctService->eventCheckAccessCode(
//            $event, request()->input('access_code')
//        )) { // event is accessing in rehearsal mode with validated code
//            return true;
//        } else if ($event->draft && $event->draft->is_reg_open == 0) {
//            // event have draft but reg is closed
//            return $this->returnError();
//        } else {
//            if ($event->draft) {
//                // here event is published and registrations are opened
//                $currentTime = Carbon::now()->timestamp;
//                $regStartTime = $this->getCarbonByDateTime($event->draft->reg_start_time)->timestamp;
//                $regEndTime = $this->getCarbonByDateTime($event->draft->reg_end_time)->timestamp;
//                $check = $currentTime >= $regStartTime && $currentTime <= $regEndTime;
//                if (!$check) {
//                    return $this->returnError();
//                }
//            }
//        }
    }

    public function isDraftEvent($event) {
        $event = $event->load(['draft' => function ($q) {
            $q->where('event_status', 2);
        }]);
        return $event->draft;
    }

    public function returnError(): bool {
        $this->error = __("kctuser::message.register_not_possible");
        return false;
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
