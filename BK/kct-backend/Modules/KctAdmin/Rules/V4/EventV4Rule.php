<?php

namespace Modules\KctAdmin\Rules\V4;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Contracts\Validation\Rule;
use Modules\KctAdmin\Entities\Event;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\UserManagement\Traits\UmHelper;

class EventV4Rule implements Rule {
    use ServicesAndRepo;
    use UmHelper;

    /**
     * @var string
     */
    private string $error = '';
    private array $errors = [];
    private ?string $attribute;
    private $value;
    private ?Event $event;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($attribute = null) {
        $this->attribute = $attribute;
        $this->event = $this->adminRepo()->eventRepository->findByEventUuid(request()->event_uuid);

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool {
        $index = $attribute;
        $this->value = $value;
        if (!$this->value) {
            return true;
        }
        if (!$this->attribute) {
            $exp = $this->setAttribute($attribute);
            $this->attribute = $attribute = $exp['attribute'];
        } else {
            $attribute = $this->attribute;
        }

        switch ($attribute) {
            case 'event_uuid':
                return $this->eventUuidValidate();
            case 'event_title':
                return $this->titleValidation();
            case 'event_start_date':
                return !($this->event && $this->event->event_type != Event::$eventType_all_day) || $this->dateValidation();
            case "event_start_time":
                return !($this->event && $this->event->event_type != Event::$eventType_all_day) || $this->startTimeValidation();
            case "event_end_time":
                return !($this->event && $this->event->event_type != Event::$eventType_all_day) || $this->endTimeValidation();
            case "moments":
                return $this->momentArrayValidate();
            case "moment_start":
                return $this->momentStartTimeValidation($index);
            case "moment_end":
                return $this->momentEndTimeValidation($index);
            case "moment_update":
                return $this->eventUuidValidate([
                    'value' => Event::$type_content, 'msg' => __('kctadmin::messages.moment_update_not_allowed')
                ]);
            default:
                return true;
        }
    }

    private function momentArrayValidate(): bool {
        if (is_array($this->value)) {
            $n = 0;
            $c = 0;
            foreach ($this->value as $m) {
                if (isset($m['moment_type'])) {
                    if ($m['moment_type'] == 1) {
                        $n += 1;
                    } else {
                        $c += 1;
                    }
                }
            }
            if ($n == 0) {
                $this->errors[] = __("kctadmin::messages.at_least_one_networking");
            }
            if ($c == 0) {
                $this->errors[] = __("kctadmin::messages.at_least_one_content");
            }
        }
        if (count($this->errors)) {
            return false;
        }
        return true;
    }


    private function checkValidTime($value): bool {
        $event = $this->adminRepo()->eventRepository->findByEventUuid(request()->input('event_uuid'));
        $startTime = explode(" ", $event->start_time);
        $endTime = explode(" ", $event->end_time);
        $value = Carbon::createFromFormat('Y-m-d H:i:s', $endTime[0] . $value)->timestamp;
        $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $startTime[0] . $startTime[1])->timestamp;
        $endTime = Carbon::createFromFormat('Y-m-d H:i:s', $endTime[0] . $endTime[1])->timestamp;
        return ($value >= $startTime && $value <= $endTime);
    }

    private function momentStartTimeValidation($index): bool {
        $index = explode(".", $index);
        $index = $index[1];
        $event = $this->adminRepo()->eventRepository->findByEventUuid(request()->input('event_uuid'));
        if ($event) {
            $date = explode(" ", $event->start_time);
            if (!$this->checkValidTime($this->value)) {
                $this->error = __("validation.after", [
                    'attribute' => $this->attribute,
                    'date'      => $event->start_time
                ]);
                return false;
            }
            $startTime = request()->input('moments.*.moment_start');
            $endTime = request()->input('moments.*.moment_end');
            $momentType = request()->input('moments.*.moment_type');
            $count = count($startTime);
            for ($i = 0; $i < $count; $i++) {
                if ($momentType[$i] == 1 || $momentType[$index] == 1) {
                    continue;
                }
                $checkStart = Carbon::createFromFormat('Y-m-d H:i:s', $date[0] . " $startTime[$index]");
                $checkEnd = Carbon::createFromFormat('Y-m-d H:i:s', $date[0] . " $endTime[$index]");
                if ($i == $index) {
                    continue;
                }
                $slotStartTime = Carbon::createFromFormat('Y-m-d H:i:s', $date[0] . " $startTime[$i]");
                $slotEndTime = Carbon::createFromFormat('Y-m-d H:i:s', $date[0] . " $endTime[$i]");
                $check1 = $slotStartTime->timestamp > $checkStart->timestamp && $slotStartTime->timestamp > $checkEnd->timestamp;
                $check2 = $slotStartTime->timestamp < $checkStart->timestamp && $slotEndTime->timestamp < $checkEnd->timestamp;
                if (!$check1) {
                    if (!$check2) {
                        $atr = ['attribute' => $this->attribute];
                        $this->error = __("validation.exists", $atr);
                        return false;
                    }
                }
            }
            return true;
        } else {
            $this->error = __("validation.exists", ['attribute' => $this->attribute]);
            return false;
        }
    }

    private function momentEndTimeValidation($index): bool {
        $index = explode(".", $index);
        $index = $index[1];
        $event = $this->adminRepo()->eventRepository->findByEventUuid(request()->input('event_uuid'));
        if ($event) {
            $date = explode(" ", $event->start_time);
            if (!$this->checkValidTime($this->value)) {
                $this->error = __("validation.before", [
                    'attribute' => $this->attribute,
                    'date'      => $event->end_time
                ]);
                return false;
            }
            $startTime = request()->input('moments.*.moment_start');
            $endTime = request()->input('moments.*.moment_end');
            $momentType = request()->input('moments.*.moment_type');
            $count = count($startTime);
            for ($i = 0; $i < $count; $i++) {
                if ($momentType[$i] == 1 || $momentType[$index] == 1) {
                    continue;
                }
                $checkStart = Carbon::createFromFormat('Y-m-d H:i:s', $date[0] . " $startTime[$index]");
                $checkEnd = Carbon::createFromFormat('Y-m-d H:i:s', $date[0] . " $endTime[$index]");
                if ($i == $index) {
                    continue;
                }
                $slotStartTime = Carbon::createFromFormat('Y-m-d H:i:s', $date[0] . " $startTime[$i]");
                $slotEndTime = Carbon::createFromFormat('Y-m-d H:i:s', $date[0] . " $endTime[$i]");
                $check1 = $slotEndTime->timestamp > $checkStart->timestamp && $slotEndTime->timestamp > $checkEnd->timestamp;
                $check2 = $slotStartTime->timestamp < $checkStart->timestamp && $slotEndTime->timestamp < $checkEnd->timestamp;
                if (!$check1) {
                    if (!$check2) {
                        $atr = ['attribute' => $this->attribute];
                        $this->error = __("validation.exists", $atr);
                        return false;
                    }
                }
            }
            return true;
        } else {
            $this->error = __("validation.exists", ['attribute' => $this->attribute]);
            return false;
        }
    }

    private function eventUuidValidate($type = null): bool {
        $check = $this->adminRepo()->eventRepository->findByEventUuid($this->value);
        if ($type && $check && $check->type != $type['value']) {
            $this->error = $type['msg'];
            return false;
        }
        if (!$check) {
            $this->error = __("validation.exists", ['attribute' => $this->attribute]);
            return false;
        }
        return true;
    }

    private function titleValidation(): bool {
        $atr = ['attribute' => 'title'];
        $val = config("kctadmin.modelConstants.events.validations");
        if (!is_string($this->value)) {
            $this->error = __("validation.string", $atr);
            return false;
        } else if (strlen($this->value) < $val['title_min']) {
            $this->error = __('validation.min.numeric', $atr);
            return false;
        }
        if (strlen($this->value) > $val['title_max']) {
            $this->error = __('validation.max.numeric', $atr);
            return false;
        }
        return true;
    }

    public function dateValidation(): bool {
        try {
            $date = Carbon::createFromFormat('Y-m-d', $this->value);
            if ($this->event && $this->event->draft->event_status === 2) {
                return true;
            } else {
                if ($date->isPast() && !$date->isToday()) {
                    $this->error = __("validation.after", [
                        'attribute' => $this->attribute,
                        'date'      => Carbon::now()->subDay(1)->toDateString()
                    ]);
                    return false;
                }
            }
        } catch (InvalidFormatException $e) {
            $this->error = __('validation.date', ['attribute' => $this->attribute]);
            return false;
        }
        return true;
    }

    public function startTimeValidation(): bool {
        try {
            $start = Carbon::createFromFormat('Y-m-d H:i:s', request()->input('event_start_date') . " $this->value");
            if ($this->event && $this->event->draft->event_status === 2) {
                return true;
            } else{
                if ($start->isPast()) {
                    $this->error = __("validation.after", [
                        'attribute' => $this->attribute,
                        'date'      => Carbon::now()->toDateTimeString()
                    ]);
                    return false;
                }
            }
        } catch (InvalidFormatException $e) {
            $this->error = __('validation.date', ['attribute' => $this->attribute]);
            return false;
        }
        return true;
    }

    public function endTimeValidation(): bool {
        $isEndPassed = false;
        try {
            $end = Carbon::createFromFormat('Y-m-d H:i:s', request()->input('event_start_date') . " $this->value");
            $isEndPassed = true;
            $start = Carbon::createFromFormat("H:i:s",
                request()->input('start_time')
            );
            if ($this->event && $this->event->draft->event_status === 2) {
                return true;
            } else {
                if ($end->isBefore($start->addMicrosecond(1))) {
                    $this->error = __("validation.after", [
                        'attribute' => $this->attribute,
                        'date'      => $start->toDateTimeString()
                    ]);
                    return false;
                }
            }
        } catch (InvalidFormatException $e) {
            if ($isEndPassed) {
                return true; // end time format created successfully so move forward
            }
            $this->error = __('validation.date', ['attribute' => $this->attribute]);
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message() {
        if (count($this->errors)) {
            return $this->errors;
        }
        return $this->error;
    }
}
