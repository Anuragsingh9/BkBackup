<?php

namespace Modules\KctAdmin\Rules;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Contracts\Validation\Rule;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\UserManagement\Traits\UmHelper;

class SpaceRule implements Rule {
    use ServicesAndRepo;
    use UmHelper;

    /**
     * @var string
     */
    private string $error = '';
    private ?string $attribute;
    private $value;
    private ?array $additional;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($attribute = null, $additional = []) {
        $this->attribute = $attribute;
        $this->additional = $additional;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool {
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
            case 'space_uuid':
                return $this->spaceUuidValidate();
            default:
                return true;
        }
    }

    private function spaceUuidValidate(): bool {
        $check = $this->adminRepo()->kctSpaceRepository->findSpaceByUuid($this->value);

        if (!$check) {
            $this->error = __("validation.exists", ['attribute' => $this->attribute]);
            return false;
        }

        if (isset($this->additional['not_default'])) {
            $defaultSpace = $check->event->spaces()->orderBy('created_at')->first();
            if ($check->space_uuid == $defaultSpace->space_uuid) {
                $this->error = __('kctadmin::messages.can_not_perform_on_default_space');
                return false;
            }
        }
        return true;
    }

    private function eventUuidValidate($type = null): bool {
        $check = $this->adminRepo()->eventRepository->findByEventUuid($this->value);
        if ($type && $check->type != $type['value']) {
            $this->error = $type['msg'];
            return false;
        }
        if (!$check) {
            $this->error = __("validation.exists", ['attribute' => $this->attribute]);
            return false;
        }
        return true;
    }

    public function dateValidation(): bool {
        try {
            $date = Carbon::createFromFormat('Y-m-d', $this->value);

            if ($date->isPast() && !$date->isToday()) {
                $this->error = __("validation.after", [
                    'attribute' => $this->attribute,
                    'date'      => Carbon::now()->subDay(1)->toDateString()
                ]);
                return false;
            }
        } catch (InvalidFormatException $e) {
            $this->error = __('validation.date', ['attribute' => $this->attribute]);
            return false;
        }
        return true;
    }

    public function startTimeValidation(): bool {
        try {
            $start = Carbon::createFromFormat('Y-m-d H:i:s', request()->input('date') . " $this->value");
            if ($start->isPast()) {
                $this->error = __("validation.after", [
                    'attribute' => $this->attribute,
                    'date'      => Carbon::now()->toDateTimeString()
                ]);
                return false;
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
            $end = Carbon::createFromFormat('Y-m-d H:i:s', request()->input('date') . " $this->value");
            $isEndPassed = true;
            $start = Carbon::createFromFormat("H:i:s",
                request()->input('start_time')
            );
            if ($end->isBefore($start->addMicrosecond(1))) {
                $this->error = __("validation.after", [
                    'attribute' => $this->attribute,
                    'date'      => $start->toDateTimeString()
                ]);
                return false;
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


    public function message(): string {
        return $this->error;
    }
}
