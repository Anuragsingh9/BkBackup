<?php

namespace Modules\KctAdmin\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Carbon;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Traits\ServicesAndRepo;

class DraftEventRule implements Rule {
    use ServicesAndRepo;
    use KctHelper;

    private $eventUuid;
    /**
     * @var mixed
     */
    private $value;
    private ?\Modules\KctAdmin\Entities\Event $event;
    /**
     * @var array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    private $error;
    private string $attribute;
    /**
     * @var mixed|string
     */
    private ?string $regDate;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(string $event_uuid, ?string $reg_date = null) {
        $this->eventUuid = $event_uuid;
        $this->regDate = $reg_date;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool {
        $this->attribute = $attribute;
        $this->value = $value;
        $event = $this->adminRepo()->eventRepository->findByEventUuid($this->eventUuid);
        $this->event = $event;
        switch ($attribute) {
            case 'event_uuid':
                return $this->validateEventUuid();
            case 'reg_start_date':
                return request()->input('event_status') == 1 || $this->validateRegStartDate();
            case 'reg_end_date':
                return request()->input('event_status') == 1 || $this->validateRegEndDate();
            case 'reg_start_time':
                return request()->input('event_status') == 1 || $this->validateRegStartTime();
            case 'reg_end_time':
                return request()->input('event_status') == 1 || $this->validateRegEndTime();
            default :
                return true;
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To validate  event event_uuid.
     * -----------------------------------------------------------------------------------------------------------------
     * @return bool
     */
    public function validateEventUuid(): bool {
        $event = $this->adminRepo()->eventRepository->findByEventUuid($this->value);
        if (!$event) {
            $this->error = __("validation.exists", [
                'attribute' => $this->attribute,
            ]);
            return false;
        }
        if ($event->type == 2) {
            $networkCount = $event->moments()->where('moment_type', 1)->count();
            $contentCount = $event->moments()->where('moment_type', '!=', 1)->count();
            if ($networkCount < 1 || $contentCount < 1) {
                $this->error = __("kctadmin::messages.there_must_one_moment");
                return false;
            }
        }
        return true;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To validate draft event register Start and End Date. The Date should be before or equal to event
     *  time.
     * -----------------------------------------------------------------------------------------------------------------
     * @return bool
     */
    public function validateRegStartDate(): bool {
        $endDate = $this->regDate;
        $eventEndDate = $this->getCarbonByDateTime($this->event->end_time)->toDateString();

        if ($this->value > $eventEndDate) {
            $this->throwDateTimeError($eventEndDate);
            return false;
        }
        if ($this->value > $endDate) {
            $this->throwDateTimeError($endDate);
            return false;
        }
        return true;
    }

    public function validateRegEndDate(): bool {
        $eventEndDate = $this->getCarbonByDateTime($this->event->end_time)->toDateString();
        if ($this->value > $eventEndDate) {
            $this->throwDateTimeError($eventEndDate);
            return false;
        }
        return true;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To validate draft event register time.
     * -----------------------------------------------------------------------------------------------------------------
     * @return bool
     */
    public function validateRegStartTime(): bool {
        $startTime = $this->value;
        $regStartDateTime = $this->getCarbonByDateTime($this->regDate, $startTime)->toDateTimeString();
        $eventEndTime = $this->getCarbonByDateTime($this->event->end_time)->toDateTimeString();
        if ($regStartDateTime > $eventEndTime) {
            $this->throwDateTimeError($eventEndTime);
            return false;
        }
        return true;
    }

    public function validateRegEndTime(): bool {
        $endTime = $this->value;
        $regEndDateTime = $this->getCarbonByDateTime($this->regDate, $endTime)->toDateTimeString();
        $eventEndTime = $this->getCarbonByDateTime($this->event->end_time)->toDateTimeString();
        if ($regEndDateTime > $eventEndTime) {
            $this->throwDateTimeError($eventEndTime);
            return false;
        }
        return true;
    }

    public function throwDateTimeError($dateTime) {
        $this->error = __("validation.before", [
            'attribute' => $this->attribute,
            'date'      => $dateTime
        ]);
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
