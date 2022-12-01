<?php

namespace Modules\KctAdmin\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Carbon;
use Modules\KctAdmin\Repositories\IEventRepository;
use Modules\KctAdmin\Traits\KctHelper;

class EventUpdateRule implements Rule {

    use KctHelper;

    private $eventRepo;
    private $errorMessage;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {
        $this->eventRepo = app(IEventRepository::class);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        $this->errorMessage = "";
        $event = $this->eventRepo->findByEventUuid($value);
        $startTime = explode(" ", $event->start_time);
        $endTime = explode(" ", $event->end_time);

        $start = $this->getCarbonByDateTime($startTime[0], $startTime[1])->timestamp;
        $end = $this->getCarbonByDateTime($endTime[0], $endTime[1])->timestamp;
        $current = Carbon::now()->timestamp;
        $this->isPastEvent($start, $end, $current);
        $this->isLiveEvent($start, $end, $current);
        if ($this->errorMessage != "") {
            return false;
        }
        return true;
    }

    public function isPastEvent($start, $end, $current) {
        if ($start < $current && $end < $current) {
            $this->errorMessage = __("kctadmin::messages.is_past_event");
        }
    }

    public function isLiveEvent($start, $end, $current) {
        if ($current >= $start && $end >= $current) {
            $this->errorMessage = __("kctadmin::messages.is_live_event");
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return $this->errorMessage;
    }
}
