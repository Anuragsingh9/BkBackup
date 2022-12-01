<?php

namespace Modules\Cocktail\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Events\Entities\Event;
use Modules\Events\Service\ValidationService;

class IsFutureEvent implements Rule
{
    private $error;
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
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $event = Event::where('event_uuid',$value)->first();
        if ($event){
            $isFutureEvent = ValidationService::getInstance()->isEventFuture($event);
            if ($isFutureEvent){
                $check = true;
            }else{
                $this->error = __('cocktail::message.event_must_future');
                $check = false;
            }
        }else{
            $this->error = __('cocktail::message.invalid_event');
            $check = false;
        }
        return $check;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->error;
    }
}
