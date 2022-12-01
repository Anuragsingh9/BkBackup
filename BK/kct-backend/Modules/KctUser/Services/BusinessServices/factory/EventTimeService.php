<?php


namespace Modules\KctUser\Services\BusinessServices\factory;


use App\Services\Service;
use Carbon\Carbon;
use Modules\Events\Service\ValidationService;
use Modules\KctUser\Services\BaseService;
use Modules\KctUser\Services\BusinessServices\IEventTimeService;

class EventTimeService implements IEventTimeService {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the event start and end time
     * This method will accept the parameter to include the opening hours in start and end time
     * if opening hours included start time will be reduced by opening before minutes
     * and end time will be increased by opening after value
     *
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @param false $includeOpeningHours
     * @return array
     */
    public function getEventTime($event, $includeOpeningHours = false) {
        $start = Carbon::createFromFormat(KctUserValidationService::DT_FORMAT, "$event->date $event->start_time");
        $end = Carbon::createFromFormat(KctUserValidationService::DT_FORMAT, "$event->date $event->end_time");

        if ($includeOpeningHours) {
            $opening = $this->getEventOpeningHours($event);
            // adding and subtracting opening hours minute from start and end time
            $start = $start->subMinute($opening['before']);
            $end = $end->addMinute($opening['after']);
        }

        return [
            'start' => $start,
            'end'   => $end,
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the event opening hours data
     * If the opening hours not found it will return the default opening hours
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return array|int[]
     */
    public function getEventOpeningHours($event) {
        $openingHours = isset($event->event_fields['opening_hours']) ? $event->event_fields['opening_hours'] : null;
        return [
            'before' => isset($openingHours['before']) ? $openingHours['before'] : 0,
            'after'  => isset($openingHours['after']) ? $openingHours['after'] : 0,
            'during' => isset($openingHours['during']) ? $openingHours['during'] : 1,
        ];
    }

}
