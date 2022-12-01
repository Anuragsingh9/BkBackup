<?php

namespace Modules\Cocktail\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Modules\Cocktail\Entities\EventSpace;
use Modules\Events\Entities\Event;

class DummySpaceMaxCountRule implements Rule
{
    private $msg;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description
     * 1. check event is dummy,if not throw validation msg and return false
     * 2. get real users in space
     * 3. get dummy users in space
     * 4. add dummy users and real users
     * 5. if sum exceeds max capacity in space throws validation message
     * -----------------------------------------------------------------------------------------------------------------
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $spaceUuid)
    {
       $check = $this->checkEventType($spaceUuid);
       if ($check){
           $space = EventSpace::with(['spaceUsers' => function ($q) {
               $q->where('user_id', '!=', Auth::user()->id);
           }])->where('space_uuid', $spaceUuid)->first();
           if ($space) { // if space not found other validation handling
               $totalUsers = $this->countAllUsersInSpace($space,$spaceUuid);
               if ($space->max_capacity && $totalUsers >= $space->max_capacity) {
                   $this->msg = __('cocktail::message.space_full');
                   return false;
               }
           }
           return true;
       }
       return true;
    }

    public function checkEventType($spaceUuid){
        $space = EventSpace::where('space_uuid',$spaceUuid)->first();
        if ($space){
            $dummyEvent = $this->checkDummyEvent($space->event_uuid);
            if ($dummyEvent){
                return true;
            }
        }
    }

    public function checkDummyEvent($eventUuid){
        $event = Event::where('event_uuid',$eventUuid)->first();
        if (isset($event->event_fields['is_dummy_event']) && $event->event_fields['is_dummy_event']){
            return true;
        }
            return false;
    }

    public function countAllUsersInSpace($space, $spaceUuid)
    {
        $dummyUsers = $this->countDummyUsersInSpace($spaceUuid);
        $users = $space->spaceUsers->count();
        return $dummyUsers + $users;
    }

    public function countDummyUsersInSpace($spaceUuid)
    {
        $dummy = EventSpace::with(['spaceDummyUsers'])->where('space_uuid', $spaceUuid)->first();
        return $dummy->spaceDummyUsers->count();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->msg;
    }
}
