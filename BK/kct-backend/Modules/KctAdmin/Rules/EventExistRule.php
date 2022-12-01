<?php

namespace Modules\KctAdmin\Rules;

use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Contracts\Validation\Rule;
use Modules\KctAdmin\Repositories\IEventRepository;
use Modules\KctAdmin\Traits\KctHelper;

class EventExistRule implements Rule {
    use KctHelper;

    private $date;
    private $start_time;
    private $end_time;
    private $title;
    private $errorMessage;
    private $eventRepo;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($date, $start_time, $end_time, $title) {
        $this->eventRepo = app(IEventRepository::class);
        $this->date = $date;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
        $this->title = $title;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        $this->errorMessage = '';
        $dateArray = explode("-", $this->date);
        if (strlen($dateArray[0]) != 4 || strlen($dateArray[1]) != 2 || strlen($dateArray[2]) != 2) {
            $this->errorMessage = __("validation.date");
            return false;
        }
        try {

            $start_time = $this->getCarbonByDateTime($this->date, $this->start_time)->toDateTimeString();
        } catch (InvalidFormatException $e) {
            // start time is not valid, so it will be handled from start time validation
            return true;
        }
        $eventExist = $this->eventRepo->isDuplicateEvent($start_time, $this->title);
        if ($eventExist) {
            $this->errorMessage = __("kctadmin::messages.event_already_exist");
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
        return $this->errorMessage;
    }
}
