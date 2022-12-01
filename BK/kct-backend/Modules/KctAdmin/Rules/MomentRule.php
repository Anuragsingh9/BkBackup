<?php

namespace Modules\KctAdmin\Rules;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Traits\ServicesAndRepo;

class MomentRule implements Rule {
    use ServicesAndRepo;
    use KctHelper;

    /**
     * @var string
     */
    private string $error = '';
    private ?string $attribute;
    private $value;
    /**
     * @var mixed
     */
    private ?string $type = null;
    private string $eventStart;
    private string $eventEnd;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($attribute = null) {
        $this->attribute = $attribute;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     * @throws Exception
     */
    public function passes($attribute, $value): bool {
        $index = $attribute;
        $this->value = $value;
        if (!$this->value) {
            return true;
        }

        $exp = $this->setAttribute($attribute);
        $this->attribute = $attribute = $exp['attribute'];
        $this->type = request()->input("moments.{$exp['index']}.moment_type");
        if (!$this->type) {
            return true;
        }
        $event = $this->adminRepo()->eventRepository->findByEventUuid(request()->event_uuid);
        $this->eventStart = Carbon::createFromFormat('Y-m-d H:i:s', $event->start_time)->toTimeString();
        $this->eventEnd = Carbon::createFromFormat('Y-m-d H:i:s', $event->end_time)->toTimeString();

        switch ($attribute) {
            case 'moderator':
                return $this->moderatorValidate($value, $exp['index']);
            case 'moment_start':
                return $this->momentStartValidate($value);
            case 'moment_end':
                return $this->momentEndValidate($value);
            default:
                return true;
        }
    }

    private function momentStartValidate($value): bool {
        $momentStart = Carbon::createFromFormat('H:i:s', $value)->toTimeString();
        $check = $momentStart >= $this->eventStart && $momentStart <= $this->eventEnd;
        if (!$check) {
            $this->throwDateTimeError();
            return false;
        }
        return true;
    }

    private function momentEndValidate($value): bool {
        $momentEnd = Carbon::createFromFormat('H:i:s', $value)->toTimeString();
        $check = $momentEnd >= $this->eventStart && $momentEnd <= $this->eventEnd;
        if (!$check) {
            $this->throwDateTimeError();
            return false;
        }
        return true;
    }


    /**
     * @throws Exception
     */
    private function moderatorValidate($value, $index): bool {
        // getting all the zoom technical settings
        $enabledSetting = $this->adminServices()->zoomService->getZoomSettings();
        if (!$enabledSetting) {
            $this->error = __('kctadmin::messages.zoom_account_expired');
            return false;
        }
        $key = $this->type == "2" ? 'webinar_data' : 'meeting_data';
        if (is_numeric($value) && (in_array($value, Arr::pluck($enabledSetting->setting_value[$key]['hosts'] ?? [], 'id')))) {
            return true;
        } else {
            $this->error = __('validation.exists', ['attribute' => 'moderator']);
            return false;
        }
    }

    public function throwDateTimeError() {
        $this->error = __('validation.between.numeric', [
            'attribute' => $this->attribute, 'min' => $this->eventStart, 'max' => $this->eventEnd
        ]);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string {
        return $this->error;
    }
}
