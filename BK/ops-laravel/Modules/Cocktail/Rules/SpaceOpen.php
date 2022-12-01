<?php

namespace Modules\Cocktail\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Modules\Cocktail\Entities\EventSpace;
use Modules\Cocktail\Services\AuthorizationService;
use Modules\Events\Entities\Event;
use Modules\Events\Service\ValidationService;

class SpaceOpen implements Rule {
    private $key;
    
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        $space = EventSpace::with('event')->where('space_uuid', $value)->first();
        if ($space && $space->event) {
            
            if(ValidationService::getInstance()->isManuallyOpen($space->event)) {
                return true;
            }
            
            $before = $space->opening_hours['before'] * 60;
            $after = $space->opening_hours['after'] * 60;
            $during = $space->opening_hours['during'];
            
            $start = Carbon::createFromFormat('Y-m-d H:i:s', "{$space->event->date} {$space->event->start_time}")->timestamp;
            $end = Carbon::createFromFormat('Y-m-d H:i:s', "{$space->event->date} {$space->event->end_time}")->timestamp;
            $current = Carbon::now()->timestamp;
            
            $isSpaceStarted = ($start - $before) <= $current;
            $isSpaceEnded = $current >= ($end + $after);
            $isEventStarted = $start <= $current;
            $isEventEnded = $current >= $end;
            
            $this->data = [
                [
                    '$isSpaceStarted' => $isSpaceStarted,
                    '$isSpaceEnded'   => $isSpaceEnded,
                    '$isEventStarted' => $isEventStarted,
                    '$isEventEnded'   => $isEventEnded,
                ], [
                    'before'  => $before,
                    'after'   => $after,
                    'during'  => $during,
                    'start'   => "{$space->event->date} {$space->event->start_time}",
                    'end'     => "{$space->event->date} {$space->event->end_time}",
                    'current' => Carbon::now()->toDateTimeString(),
                ],
            ];
            
            if (!$isSpaceStarted) { // the space has not yet started
                $this->key = __('cocktail::message.space_not_started');
                $result =  false;
            } else if ($isSpaceEnded) { // the space has started and also ended
                $this->key = __('cocktail::message.space_end');
                $result =  false;
            } else { // the space started and not ended
                if (!$during && $isEventStarted && !$isEventEnded) { // if during not allowed check current time is during event or not
                    // during not allowed and current time is during the event so discard the action
                    $this->key = __('cocktail::message.space_during_not_allowed');
                    $result =  false;
                } else { // space started || space not ended
                    $result =  true;
                }
            }
        } else {
            $this->key = __('cocktail::message.invalid_space');
            $result =  false;
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
